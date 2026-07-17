<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundProcessed extends Notification
{
    public function __construct(protected Subscription $subscription) {}

    /**
     * Always mail, regardless of preference — this is confirmation that
     * money was actually sent back, not an operational FYI.
     *
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
            ->subject('Your refund has been processed')
            ->greeting("Hi {$notifiable->name},")
            ->line("We've refunded ₦".number_format($subscription->refund_amount)." for your unused time on the {$subscription->name} plan.")
            ->line('This should reflect on your original payment method within a few business days, depending on your bank.')
            ->action('View Billing', url('/user/billing'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $subscription = $this->subscription;

        return FilamentNotification::make()
            ->title('Refund processed')
            ->body('₦'.number_format($subscription->refund_amount).' has been refunded to your original payment method.')
            ->success()
            ->getDatabaseMessage();
    }
}
