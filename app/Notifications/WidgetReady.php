<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WidgetReady extends Notification
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
            ->subject('Your chat widget is ready to go live')
            ->greeting("Hi {$notifiable->name},")
            ->line("Good news — we've finished setting up your chat widget on our end. It's ready to embed.")
            ->action('Get Your Embed Code', url('/user/widget-settings'))
            ->line('Paste the snippet onto your website and your AI assistant will start answering visitors right away.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Widget ready to go live')
            ->body('Setup is complete on our end — head to Widget Settings to get your embed code.')
            ->success()
            ->getDatabaseMessage();
    }
}
