<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionEndingSoon extends Notification
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
        $isTrial = $this->subscription->plan === 'trial';
        $daysLeft = now()->startOfDay()->diffInDays($this->subscription->end_date->copy()->startOfDay());

        return (new MailMessage)
            ->subject($isTrial ? 'Your free trial ends soon' : 'Your subscription ends soon')
            ->greeting("Hi {$notifiable->name},")
            ->line($isTrial
                ? "Your 14-day free trial ends in {$daysLeft} days, on ".$this->subscription->end_date->format('l, F j, Y').'.'
                : "Your {$this->subscription->name} subscription ends in {$daysLeft} days, on ".$this->subscription->end_date->format('l, F j, Y').'.')
            ->line('Subscribe to a plan before then to keep your services running without any interruption.')
            ->action('View Billing', url('/user/billing'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $isTrial = $this->subscription->plan === 'trial';

        return FilamentNotification::make()
            ->title($isTrial ? 'Your free trial ends soon' : 'Your subscription ends soon')
            ->body('Ends '.$this->subscription->end_date->format('M j, Y').' — subscribe to a plan to avoid any interruption.')
            ->warning()
            ->getDatabaseMessage();
    }
}
