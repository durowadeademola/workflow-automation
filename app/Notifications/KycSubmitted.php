<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class KycSubmitted extends Notification
{
    public function __construct(protected KycSubmission $submission) {}

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
            ->title('New KYC submission')
            ->body("{$this->submission->client->name} submitted identity verification documents for review.")
            ->actions([
                Action::make('view')->button()->url("/admin/kyc-submissions/{$this->submission->id}"),
            ])
            ->getDatabaseMessage();
    }
}
