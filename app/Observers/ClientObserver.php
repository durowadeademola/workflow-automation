<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\ClientApproved;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class ClientObserver
{
    /**
     * Handle the Client "created" event. Covers both onboarding paths: a
     * self-registered business starts "pending" and gets its trial later
     * (see updated()), while an admin creating one directly in Filament
     * defaults to "active" immediately — that business should start its
     * trial right away too, not miss out just because it skipped approval.
     */
    public function created(Client $client): void
    {
        if ($client->status === 'active') {
            $this->grantTrialIfEligible($client);

            return;
        }

        if ($client->status !== 'pending') {
            return;
        }

        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            Notification::make()
                ->title('New business awaiting approval')
                ->body("{$client->name} just self-registered and can't log in until you approve them.")
                ->warning()
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(fn () => "/admin/clients/{$client->id}/edit"),
                ])
                ->sendToDatabase($admin);
        }
    }

    /**
     * Handle the Client "updated" event. When a business flips to "active"
     * (approval, or reinstatement after being marked inactive), let its own
     * users know they can now log in — they have no other way to find out.
     */
    public function updated(Client $client): void
    {
        if (! $client->wasChanged('status')) {
            return;
        }

        if ($client->status !== 'active' || $client->getOriginal('status') === 'active') {
            return;
        }

        $this->grantTrialIfEligible($client);

        $recipients = User::where('client_id', $client->id)
            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        NotificationFacade::send($recipients, new ClientApproved());
    }

    /**
     * A business gets one free 14-day trial the first time it's approved —
     * never again, even if it's later deactivated and reinstated. This is
     * just a subscription row like any other, so the widget's existing
     * `hasActiveSubscription()` check and the daily expiry job both apply
     * to it with zero special-casing.
     */
    private function grantTrialIfEligible(Client $client): void
    {
        if (Subscription::where('client_id', $client->id)->exists()) {
            return;
        }

        Subscription::create([
            'client_id' => $client->id,
            'plan' => 'trial',
            'name' => 'Free Trial',
            'amount' => 0,
            'status' => 'active',
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(14),
        ]);
    }
}
