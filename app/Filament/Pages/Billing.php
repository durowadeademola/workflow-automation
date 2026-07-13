<?php

namespace App\Filament\Pages;

use App\Models\Plan;
use App\Models\Subscription;
use App\Services\PaystackService;
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
        return Plan::active()->get();
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

        $planRecord = Plan::active()->where('slug', $plan)->first();

        abort_unless($planRecord, 404);

        $subscription = Subscription::create([
            'client_id' => $client->id,
            'plan_id' => $planRecord->id,
            'plan' => $planRecord->slug,
            'amount' => $planRecord->amount,
            'name' => $planRecord->name,
            'status' => 'pending',
            'is_active' => false,
            'paystack_reference' => 'BF-'.strtoupper(Str::random(14)),
        ]);

        try {
            $result = app(PaystackService::class)->initializeTransaction([
                'email' => $client->email,
                'amount' => $planRecord->amount * 100, // kobo
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
