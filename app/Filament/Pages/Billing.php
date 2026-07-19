<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\RefundRequested;
use App\Services\PaystackService;
use App\Services\SubscriptionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Str;

class Billing extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CreditCard;

    protected static ?int $navigationSort = 15;

    protected string $view = 'filament.pages.billing';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return (bool) $user && $user->is_client;
    }

    public function mount(): void
    {
        $notice = session('paystack_notice');

        if ($notice) {
            Notification::make()
                ->title($notice['message'])
                ->status($notice['type'])
                ->send();
        }
    }

    public function getClient()
    {
        return Auth::user()->client;
    }

    public function getCurrentSubscription(): ?Subscription
    {
        $client = $this->getClient();

        if (! $client) {
            return null;
        }

        return Subscription::where('client_id', $client->id)
            ->where('status', 'active')
            ->where('end_date', '>=', now()->startOfDay())
            ->latest('end_date')
            ->first();
    }

    public function getRecentSubscriptions()
    {
        $client = $this->getClient();

        if (! $client) {
            return collect();
        }

        return Subscription::where('client_id', $client->id)
            ->latest()
            ->limit(10)
            ->get();
    }

    public function getPlans()
    {
        return Plan::active()->forClient($this->getClient())->get();
    }

    /**
     * @return array{used: int, limit: ?int}
     */
    public function getMessageUsage(): array
    {
        $client = $this->getClient();

        if (! $client) {
            return ['used' => 0, 'limit' => 0];
        }

        return [
            'used' => $client->messagesUsedInCurrentPeriod(),
            'limit' => $client->messageLimitForCurrentPlan(),
        ];
    }

    /**
     * @return array{used: int, limit: ?int}
     */
    public function getAppointmentUsage(): array
    {
        $client = $this->getClient();

        if (! $client) {
            return ['used' => 0, 'limit' => 0];
        }

        return [
            'used' => $client->appointmentsBookedInCurrentPeriod(),
            'limit' => $client->appointmentLimitForCurrentPlan(),
        ];
    }

    /**
     * @return array{used: int, limit: ?int}
     */
    public function getLeadUsage(): array
    {
        $client = $this->getClient();

        if (! $client) {
            return ['used' => 0, 'limit' => 0];
        }

        return [
            'used' => $client->qualifiedLeadsInCurrentPeriod(),
            'limit' => $client->leadLimitForCurrentPlan(),
        ];
    }

    /**
     * The Naira credit switching plans right now would carry over — the
     * larger of two mutually-exclusive components (a subscription either
     * still has remaining days, or it's already run its course, never
     * both): unused time on a still-active subscription being switched
     * mid-cycle, or unused message capacity on one that already expired
     * with room to spare. ₦0 if there's nothing to credit, or it's the
     * free trial.
     */
    public function getProratedCredit(): int
    {
        $dayBasedCredit = app(SubscriptionService::class)->calculateProratedCredit($this->getCurrentSubscription());

        $client = $this->getClient();
        $messageCredit = $client
            ? $this->calculateMessageProrationCredit($client, $client->mostRecentSubscription())
            : 0;

        return $dayBasedCredit + $messageCredit;
    }

    /**
     * Unused message capacity from a subscription that already ran its
     * full course becomes a credit on the next one — the message-quota
     * equivalent of the day-based credit above, for a plan that used up
     * all its time rather than being switched early (those two never
     * overlap: calculateProratedCredit() already returns 0 once a
     * subscription isn't still active). Skipped for trials (nothing was
     * actually paid), unlimited plans (nothing to prorate), and anything
     * already refunded (would double-count the unused-time compensation
     * it already got).
     */
    private function calculateMessageProrationCredit(Client $client, ?Subscription $previous): int
    {
        if (! $previous || $previous->refund_status || $previous->plan === 'trial' || $previous->amount <= 0) {
            return 0;
        }

        if (! $previous->end_date || $previous->end_date->isFuture()) {
            return 0;
        }

        $messageLimit = $client->messageLimitForSubscription($previous);

        if (! $messageLimit) {
            return 0;
        }

        $unusedMessages = max(0, $messageLimit - $client->messagesUsedInSubscriptionPeriod($previous));
        $creditPerMessage = $previous->amount / $messageLimit;

        return (int) round($unusedMessages * $creditPerMessage);
    }

    public function getSwitchConfirmationMessage(string $planSlug, string $cycle = 'monthly'): string
    {
        $planRecord = Plan::active()->forClient($this->getClient())->where('slug', $planSlug)->first();
        $credit = $this->getProratedCredit();

        if (! $planRecord || $credit <= 0) {
            return "Switch to {$planRecord?->name}?";
        }

        $price = $cycle === 'yearly' ? $planRecord->yearly_effective_price : $planRecord->effective_price;
        $finalCharge = max(0, $price - $credit);

        return "Your remaining time on your current plan is worth a ₦".number_format($credit)." credit. "
            .($finalCharge > 0
                ? "You'll pay ₦".number_format($finalCharge)." today instead of ₦".number_format($price).'.'
                : "That fully covers {$planRecord->name} — you won't be charged anything today.");
    }

    /**
     * Wraps subscribe() in a Filament modal instead of the browser's native
     * confirm() dialog (triggered via wire:confirm previously). Always
     * confirms now — even a fresh subscribe carries the processing-fee
     * disclosure below, so it can no longer skip the modal the way it used
     * to when there was nothing plan-switch-specific to say.
     */
    public function subscribeAction(): Action
    {
        return Action::make('subscribe')
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => $this->getCurrentSubscription() ? 'Confirm plan change' : 'Confirm subscription')
            ->modalDescription(function (array $arguments): string {
                $feeNote = 'Additional processing fees apply.';

                if ($this->getCurrentSubscription()) {
                    return $this->getSwitchConfirmationMessage($arguments['plan'] ?? '', $arguments['cycle'] ?? 'monthly').' '.$feeNote;
                }

                return $feeNote;
            })
            ->modalSubmitActionLabel('Proceed to payment')
            ->action(fn (array $arguments) => $this->subscribe($arguments['plan'], $arguments['cycle'] ?? 'monthly'));
    }

    public function subscribe(string $plan, string $cycle = 'monthly')
    {
        $client = $this->getClient();

        if (! $client) {
            Notification::make()->title('Your account is not linked to a business.')->danger()->send();

            return;
        }

        if (! $client->email) {
            Notification::make()->title('Add an email to your business profile before subscribing.')->danger()->send();

            return;
        }

        $cycle = $cycle === 'yearly' ? 'yearly' : 'monthly';

        $planRecord = Plan::active()->forClient($client)->where('slug', $plan)->first();

        abort_unless($planRecord, 404);

        $price = $cycle === 'yearly' ? $planRecord->yearly_effective_price : $planRecord->effective_price;

        $credit = $this->getProratedCredit();
        $finalCharge = max(0, $price - $credit);

        // Not currentSubscription() — by the time a client resubscribes,
        // the period being rolled over from has very often already expired
        // (that's usually *why* they're resubscribing), and currentSubscription()
        // would no longer find it.
        $previousSubscription = $client->mostRecentSubscription();
        [$rolledOverAppointments, $rolledOverLeads] = $this->calculateRollover($client, $previousSubscription);

        $subscription = Subscription::create([
            'client_id' => $client->id,
            'plan_id' => $planRecord->id,
            'plan' => $planRecord->slug,
            'billing_cycle' => $cycle,
            'amount' => $price,
            'credit_applied' => $credit,
            'name' => $planRecord->name,
            'status' => 'pending',
            'is_active' => false,
            'paystack_reference' => $finalCharge > 0 ? 'BF-'.strtoupper(Str::random(14)) : null,
            'rolled_over_appointments' => $rolledOverAppointments,
            'rolled_over_leads' => $rolledOverLeads,
        ]);

        // The credit fully covers this plan — no payment needed at all.
        if ($finalCharge <= 0) {
            app(SubscriptionService::class)->activateFree($subscription);

            Notification::make()
                ->title("You're now on {$planRecord->name} — fully covered by your remaining credit.")
                ->success()
                ->send();

            return;
        }

        try {
            $result = app(PaystackService::class)->initializeTransaction([
                'email' => $client->email,
                'amount' => $finalCharge * 100, // kobo
                'reference' => $subscription->paystack_reference,
                'callback_url' => route('paystack.callback'),
                'metadata' => [
                    'subscription_id' => $subscription->id,
                    'client_id' => $client->id,
                    'plan' => $planRecord->slug,
                    'cycle' => $cycle,
                ],
            ]);

            $authorizationUrl = $result['data']['authorization_url'] ?? null;

            abort_unless($authorizationUrl, 502);
        } catch (\Throwable $e) {
            $subscription->update(['status' => 'cancelled']);
            Notification::make()->title('Could not start checkout. Please try again shortly.')->danger()->send();

            return;
        }

        return redirect()->away($authorizationUrl);
    }

    /**
     * Appointments/leads only carry into the next subscription when the
     * message cap — not just ordinary low demand — is what left them
     * unused, so plan tiers still mean something instead of slowly becoming
     * unlimited for anyone who under-uses their plan. Only ever pulled from
     * the immediately preceding subscription — it doesn't itself roll over
     * again if left unused a second time.
     *
     * @return array{0: int, 1: int} [rolled_over_appointments, rolled_over_leads]
     */
    private function calculateRollover(Client $client, ?Subscription $previous): array
    {
        if (! $previous || ! $previous->limit_reached_notified_at) {
            return [0, 0];
        }

        $appointmentLimit = $client->appointmentLimitForSubscription($previous);
        $leadLimit = $client->leadLimitForSubscription($previous);

        $unusedAppointments = $appointmentLimit === null
            ? 0
            : max(0, $appointmentLimit - $client->appointmentsBookedInSubscriptionPeriod($previous));

        $unusedLeads = $leadLimit === null
            ? 0
            : max(0, $leadLimit - $client->qualifiedLeadsInSubscriptionPeriod($previous));

        return [$unusedAppointments, $unusedLeads];
    }

    /**
     * A refund is only offered when this subscription was actually charged
     * directly through Paystack — a trial, or one fully covered by rollover
     * credit from a plan switch, never took a real payment, so there's
     * nothing to send back.
     */
    private function isRefundEligible(?Subscription $subscription): bool
    {
        return (bool) $subscription?->paystack_transaction_id;
    }

    /**
     * Cancels the current subscription. Since billing here is one-off
     * charges with no auto-renewal (nothing ever re-charges a client
     * automatically), "cancel" doesn't stop a future payment — it just
     * marks intent. Two paths from here: keep access until end_date with
     * no refund (the default, and the only option when there's nothing to
     * refund), or end access right now and request a refund for the unused
     * remainder — which requires an admin to actually process the Paystack
     * refund before any money moves. Subscribing to any plan afterward
     * naturally supersedes this, since it creates a fresh subscription
     * untouched by cancelled_at.
     */
    public function cancelAction(): Action
    {
        return Action::make('cancel')
            ->requiresConfirmation()
            ->modalHeading('Cancel subscription?')
            ->modalDescription(function (): string {
                $subscription = $this->getCurrentSubscription();

                if (! $subscription) {
                    return 'No active subscription to cancel.';
                }

                return $this->isRefundEligible($subscription)
                    ? 'Choose what happens next.'
                    : "You'll keep access until {$subscription->end_date->format('M j, Y')} — no refund for unused time, and it won't renew after that.";
            })
            ->schema(function (): array {
                $subscription = $this->getCurrentSubscription();
                $fields = [];

                if ($this->isRefundEligible($subscription)) {
                    $fields[] = Radio::make('refund_choice')
                        ->label('What would you like to do?')
                        ->options([
                            'keep' => "Keep access until {$subscription->end_date->format('M j, Y')} — no refund",
                            'refund' => 'Stop now and request a refund for the unused time',
                        ])
                        ->default('keep')
                        ->required();
                }

                $fields[] = Textarea::make('reason')
                    ->label('Why are you cancelling? (optional)')
                    ->helperText('Helps us understand what we could do better — visible only to our team.')
                    ->rows(3);

                return $fields;
            })
            ->modalSubmitActionLabel('Cancel subscription')
            ->color('danger')
            ->action(fn (array $data) => $this->cancel($data['reason'] ?? null, $data['refund_choice'] ?? 'keep'));
    }

    public function cancel(?string $reason = null, string $refundChoice = 'keep'): void
    {
        $subscription = $this->getCurrentSubscription();

        if (! $subscription) {
            Notification::make()->title('No active subscription to cancel.')->danger()->send();

            return;
        }

        if ($subscription->cancelled_at) {
            return;
        }

        if ($refundChoice === 'refund' && $this->isRefundEligible($subscription)) {
            $this->requestRefund($subscription, $reason);

            return;
        }

        $subscription->update([
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        Notification::make()
            ->title('Subscription cancelled — you\'ll keep access until '.$subscription->end_date->format('M j, Y').'.')
            ->success()
            ->send();
    }

    /**
     * Ends access immediately (rather than at end_date) since the client
     * explicitly chose money back over continued use, and flags the
     * subscription for an admin to actually process the Paystack refund —
     * nothing here moves real money by itself. The original end_date is
     * preserved so access can be restored if the request is rejected
     * instead of processed.
     */
    private function requestRefund(Subscription $subscription, ?string $reason): void
    {
        $totalDays = max(1, (int) $subscription->start_date->diffInDays($subscription->end_date));
        $remainingDays = max(0, (int) now()->startOfDay()->diffInDays($subscription->end_date, false));
        $baseAmount = $subscription->paystack_amount_charged ?? $subscription->amount;
        $refundAmount = (int) round($baseAmount * ($remainingDays / $totalDays));

        $subscription->update([
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'refund_status' => 'requested',
            'refund_requested_at' => now(),
            'refund_amount' => $refundAmount,
            'refund_original_end_date' => $subscription->end_date,
            // currentSubscription() compares end_date against now()->startOfDay(),
            // and end_date itself has no time-of-day precision — so "today"
            // would still read as active for the rest of today. Yesterday is
            // the only value that's unambiguously excluded immediately.
            'end_date' => now()->subDay(),
        ]);

        $admins = User::where('is_admin', true)->get();

        if ($admins->isNotEmpty()) {
            LaravelNotification::send($admins, new RefundRequested($subscription));
        }

        Notification::make()
            ->title('Refund requested — your access has ended, and our team will process your refund of ₦'.number_format($refundAmount).' shortly.')
            ->success()
            ->send();
    }
}
