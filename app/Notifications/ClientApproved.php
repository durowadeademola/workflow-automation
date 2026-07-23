<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientApproved extends Notification
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
            ->subject("You're approved — Welcome to Blueflow")
            ->greeting("Hi {$notifiable->name},")
            ->line('Good news — your business has been approved and your dashboard is now active.')
            ->action('Log In', url('/user/login'))
            ->line('You can subscribe to a plan and set up your chat widget from your dashboard.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Welcome to Blueflow')
            ->body('Your business has been approved. Head to Widget Settings and Billing to finish setting up.')
            ->success()
            ->getDatabaseMessage();
    }
}
