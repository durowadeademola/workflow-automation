<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpired extends Notification
{
    public function __construct(protected Subscription $subscription) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isTrial = $this->subscription->plan === 'trial';

        return (new MailMessage)
            ->subject($isTrial ? 'Your free trial has ended' : 'Your subscription has ended')
            ->greeting("Hi {$notifiable->name},")
            ->line($isTrial
                ? 'Your 14-day free trial has ended, and your chat widget has been paused.'
                : "Your {$this->subscription->name} subscription has ended, and your chat widget has been paused.")
            ->line('Subscribe to a plan to get your widget answering visitors again.')
            ->action('Subscribe Now', url('/user/billing'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $isTrial = $this->subscription->plan === 'trial';

        return FilamentNotification::make()
            ->title($isTrial ? 'Your free trial has ended' : 'Your subscription has ended')
            ->body('Your chat widget is paused until you subscribe to a plan.')
            ->danger()
            ->getDatabaseMessage();
    }
}
