<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientApproved;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

class ClientObserver
{
    /**
     * Handle the Client "created" event.
     */
    public function created(Client $client): void
    {
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

        $recipients = User::where('client_id', $client->id)
            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        NotificationFacade::send($recipients, new ClientApproved());
    }
}
