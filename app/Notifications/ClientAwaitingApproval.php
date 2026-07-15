<?php

namespace App\Notifications;

use App\Models\Client;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class ClientAwaitingApproval extends Notification
{
    public function __construct(protected Client $client) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('New business awaiting approval')
            ->warning()
            ->body("{$this->client->name} just self-registered and can't log in until you approve them.")
            ->actions([
                Action::make('view')->button()->url("/admin/clients/{$this->client->id}/edit"),
            ])
            ->getDatabaseMessage();
    }
}
