<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRejected extends Notification
{
    public function __construct(protected Subscription $subscription) {}

    /**
     * Always mail — this also explains that their access has been
     * restored, which they need to know regardless of preference.
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
        $restored = $subscription->refund_original_end_date && $subscription->refund_original_end_date->isFuture();

        return (new MailMessage)
            ->subject('Update on your refund request')
            ->greeting("Hi {$notifiable->name},")
            ->line("We weren't able to process your refund request for the {$subscription->name} plan.")
            ->when($subscription->refund_rejection_reason, fn ($mail) => $mail->line("Reason: {$subscription->refund_rejection_reason}"))
            ->when(
                $restored,
                fn ($mail) => $mail->line('Your access has been restored and will continue until '.$subscription->refund_original_end_date->format('M j, Y').'.'),
            )
            ->action('View Billing', url('/user/billing'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $subscription = $this->subscription;

        return FilamentNotification::make()
            ->title('Refund request declined')
            ->body($subscription->refund_rejection_reason ?: 'Your access has been restored.')
            ->danger()
            ->getDatabaseMessage();
    }
}
