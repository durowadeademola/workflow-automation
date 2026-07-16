<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PaystackService;
use App\Services\SubscriptionService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
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
     * The Naira credit switching to $planSlug right now would carry over
     * from the unused portion of the current subscription (₦0 if there's
     * no current subscription, or it's the free trial).
     */
    public function getProratedCredit(): int
    {
        return app(SubscriptionService::class)->calculateProratedCredit($this->getCurrentSubscription());
    }

    public function getSwitchConfirmationMessage(string $planSlug): string
    {
        $planRecord = Plan::active()->forClient($this->getClient())->where('slug', $planSlug)->first();
        $credit = $this->getProratedCredit();

        if (! $planRecord || $credit <= 0) {
            return "Switch to {$planRecord?->name}?";
        }

        $finalCharge = max(0, $planRecord->amount - $credit);

        return "Your remaining time on your current plan is worth a ₦".number_format($credit)." credit. "
            .($finalCharge > 0
                ? "You'll pay ₦".number_format($finalCharge)." today instead of ₦".number_format($planRecord->amount).'.'
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
                    return $this->getSwitchConfirmationMessage($arguments['plan'] ?? '').' '.$feeNote;
                }

                return $feeNote;
            })
            ->modalSubmitActionLabel('Proceed to payment')
            ->action(fn (array $arguments) => $this->subscribe($arguments['plan']));
    }

    public function subscribe(string $plan)
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

        $planRecord = Plan::active()->forClient($client)->where('slug', $plan)->first();

        abort_unless($planRecord, 404);

        $credit = $this->getProratedCredit();
        $finalCharge = max(0, $planRecord->amount - $credit);

        $previousSubscription = $client->currentSubscription();
        [$rolledOverAppointments, $rolledOverLeads] = $this->calculateRollover($client, $previousSubscription);

        $subscription = Subscription::create([
            'client_id' => $client->id,
            'plan_id' => $planRecord->id,
            'plan' => $planRecord->slug,
            'amount' => $planRecord->amount,
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

        $appointmentLimit = $client->appointmentLimitForCurrentPlan();
        $leadLimit = $client->leadLimitForCurrentPlan();

        $unusedAppointments = $appointmentLimit === null
            ? 0
            : max(0, $appointmentLimit - $client->appointmentsBookedInCurrentPeriod());

        $unusedLeads = $leadLimit === null
            ? 0
            : max(0, $leadLimit - $client->qualifiedLeadsInCurrentPeriod());

        return [$unusedAppointments, $unusedLeads];
    }

    /**
     * Cancels the current subscription. Since billing here is one-off
     * charges with no auto-renewal (nothing ever re-charges a client
     * automatically), "cancel" doesn't stop a future payment — it just
     * marks intent and hides the renewal nudge. Access keeps running until
     * end_date (already paid for), with no refund or credit for unused
     * time. Subscribing to any plan afterward naturally supersedes this,
     * since it creates a fresh subscription untouched by cancelled_at.
     */
    public function cancelAction(): Action
    {
        return Action::make('cancel')
            ->requiresConfirmation()
            ->modalHeading('Cancel subscription?')
            ->modalDescription(function (): string {
                $subscription = $this->getCurrentSubscription();

                return $subscription
                    ? "You'll keep access until {$subscription->end_date->format('M j, Y')} — no refund for unused time, and it won't renew after that."
                    : 'No active subscription to cancel.';
            })
            ->schema([
                Textarea::make('reason')
                    ->label('Why are you cancelling? (optional)')
                    ->helperText('Helps us understand what we could do better — visible only to our team.')
                    ->rows(3),
            ])
            ->modalSubmitActionLabel('Cancel subscription')
            ->color('danger')
            ->action(fn (array $data) => $this->cancel($data['reason'] ?? null));
    }

    public function cancel(?string $reason = null): void
    {
        $subscription = $this->getCurrentSubscription();

        if (! $subscription) {
            Notification::make()->title('No active subscription to cancel.')->danger()->send();

            return;
        }

        if ($subscription->cancelled_at) {
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
}
