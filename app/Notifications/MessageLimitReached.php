<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MessageLimitReached extends Notification
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
            ->subject('Your monthly message limit has been reached')
            ->greeting("Hi {$notifiable->name},")
            ->line('Your chat widget has used up all the messages included in your current plan for this billing period, so it has paused for the rest of this cycle.')
            ->line('Upgrade to a higher plan to keep it running right away.')
            ->action('Upgrade Plan', url('/admin/billing'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Message limit reached')
            ->body('Your chat widget is paused for the rest of this billing period. Upgrade your plan to continue.')
            ->danger()
            ->getDatabaseMessage();
    }
}
