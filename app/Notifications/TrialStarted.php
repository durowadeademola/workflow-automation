<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialStarted extends Notification
{
    public function __construct(protected Subscription $subscription) {}

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
            ->subject('Your 14-day free trial has started')
            ->greeting("Hi {$notifiable->name},")
            ->line("Your free trial is live — your chat widget can start answering visitors right away, no payment needed yet.")
            ->line('Trial ends: '.$this->subscription->end_date->format('l, F j, Y'))
            ->action('Set Up Your Widget', url('/user/widget-settings'))
            ->line("We'll remind you before it ends, and you can subscribe to a plan any time from Billing to keep it running afterward.");
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Your free trial has started')
            ->body('Ends '.$this->subscription->end_date->format('M j, Y').' — set up your widget to make the most of it.')
            ->success()
            ->getDatabaseMessage();
    }
}
