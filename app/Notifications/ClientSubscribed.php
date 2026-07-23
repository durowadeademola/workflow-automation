<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells admins whenever a client's subscription actually activates — first
 * purchase or a later manual renewal, since this app has no auto-recurring
 * billing (see SubscriptionService::sendInvoice(), the single place this is
 * dispatched from). Never fired for the automatic trial grant, which is
 * created directly rather than through that activation path.
 */
class ClientSubscribed extends Notification
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

        return (new MailMessage)
            ->subject('New subscription payment')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$subscription->client?->name} just subscribed to the {$subscription->name} plan.")
            ->line('Amount paid: ₦'.number_format($subscription->amount))
            ->action('View Subscription', url('/admin/subscriptions'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $subscription = $this->subscription;

        return FilamentNotification::make()
            ->title('New subscription payment')
            ->body("{$subscription->client?->name} — {$subscription->name} — ₦".number_format($subscription->amount))
            ->success()
            ->getDatabaseMessage();
    }
}
