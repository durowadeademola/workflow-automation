<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PaystackService;
use App\Services\SubscriptionService;
use BackedEnum;
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
}
