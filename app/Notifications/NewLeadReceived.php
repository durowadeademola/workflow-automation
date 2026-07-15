<?php

namespace App\Notifications;

use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class NewLeadReceived extends Notification
{
    public function __construct(protected Lead $lead) {}

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
            ->title('New Lead Received')
            ->success()
            ->body("{$this->lead->name} submitted the contact form.")
            ->actions([
                Action::make('view')->button()->url("/admin/leads/{$this->lead->id}/edit"),
            ])
            ->getDatabaseMessage();
    }
}
