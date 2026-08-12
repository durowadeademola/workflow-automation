<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells admins a refund attempt actually failed at Paystack (e.g.
 * insufficient balance in Blueflow's Paystack wallet — refunds are deducted
 * from that balance, not pulled from the settlement bank account). Sent as
 * a persistent notification rather than relying on the one-time toast the
 * admin who clicked "Process Refund" saw, so it isn't missed if nobody's
 * watching right then. The client's access was already restored
 * automatically; this is what needs a human to actually resolve.
 */
class RefundProcessingFailed extends Notification
{
    public function __construct(protected Subscription $subscription, protected string $reason) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subscription = $this->subscription;

        return (new MailMessage)
            ->subject('Refund failed — action needed')
            ->greeting("Hi {$notifiable->name},")
            ->line("The refund for {$subscription->client?->name}'s {$subscription->name} subscription (₦".number_format($subscription->refund_amount).') failed to process.')
            ->line("Paystack said: {$this->reason}")
            ->line("The client's access has been automatically restored in the meantime. Retry from Subscriptions once resolved (e.g. after your next Paystack settlement, or topping up your Paystack balance).")
            ->action('View Subscription', url('/admin/subscriptions'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $subscription = $this->subscription;

        return FilamentNotification::make()
            ->title('Refund failed')
            ->body("{$subscription->client?->name} — ₦".number_format($subscription->refund_amount).' — '.$this->reason)
            ->danger()
            ->getDatabaseMessage();
    }
}
