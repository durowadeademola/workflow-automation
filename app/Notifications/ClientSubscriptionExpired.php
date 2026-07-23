<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * The admin-facing counterpart to SubscriptionExpired (which tells the
 * client themselves) — lets admins know a client's widget just got paused,
 * so a lapsed paying customer doesn't go unnoticed until someone happens to
 * check the Subscriptions list.
 */
class ClientSubscriptionExpired extends Notification
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
        $subscription = $this->subscription;
        $isTrial = $subscription->plan === 'trial';

        return (new MailMessage)
            ->subject($isTrial ? "A client's free trial has ended" : "A client's subscription has ended")
            ->greeting("Hi {$notifiable->name},")
            ->line($isTrial
                ? "{$subscription->client?->name}'s 14-day free trial has ended, and their chat widget has been paused."
                : "{$subscription->client?->name}'s {$subscription->name} subscription has ended, and their chat widget has been paused.")
            ->action('View Client', url('/admin/subscriptions'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $subscription = $this->subscription;
        $isTrial = $subscription->plan === 'trial';

        return FilamentNotification::make()
            ->title($isTrial ? "A client's free trial has ended" : "A client's subscription has ended")
            ->body("{$subscription->client?->name} — widget paused until they subscribe.")
            ->warning()
            ->getDatabaseMessage();
    }
}
