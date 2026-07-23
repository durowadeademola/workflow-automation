<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycSubmitted extends Notification
{
    public function __construct(protected KycSubmission $submission) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->email_notifications_enabled
            ? ['mail', 'database']
            : ['database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New KYC submission awaiting review')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->submission->client->name} submitted identity verification documents for review.")
            ->action('Review Submission', url("/admin/kyc-submissions/{$this->submission->id}"));
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
