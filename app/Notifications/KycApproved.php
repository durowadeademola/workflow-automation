<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycApproved extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your identity verification was approved')
            ->greeting("Hi {$notifiable->name},")
            ->line('Good news — your submitted KYC documents have been reviewed and approved.')
            ->action('View Status', url('/admin/kyc-verification'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('KYC approved')
            ->body('Your identity verification documents have been approved.')
            ->success()
            ->getDatabaseMessage();
    }
}
