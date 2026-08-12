<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the client when their requested refund couldn't be completed yet
 * (a Paystack-side failure, e.g. insufficient balance) — deliberately vague
 * about the technical reason, since that's an internal/operational problem
 * on Blueflow's end, not something the client did wrong (the real detail
 * goes to admins instead, via RefundProcessingFailed). Access is restored
 * in the meantime so the client isn't left paying the cost of a delay that
 * isn't their fault.
 */
class RefundDelayed extends Notification
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
        $subscription = $this->subscription;
        $restored = $subscription->refund_original_end_date && $subscription->refund_original_end_date->isFuture();

        return (new MailMessage)
            ->subject('Update on your refund request')
            ->greeting("Hi {$notifiable->name},")
            ->line("We're still working on processing your refund for the {$subscription->name} plan — it's taking a little longer than expected.")
            ->when(
                $restored,
                fn ($mail) => $mail->line('In the meantime, your access has been restored and will continue until '.$subscription->refund_original_end_date->format('M j, Y').'.'),
            )
            ->line("We'll follow up as soon as it's completed — no action is needed from you.");
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Your refund is delayed')
            ->body('Your access has been restored in the meantime — we\'ll follow up once it\'s processed.')
            ->warning()
            ->getDatabaseMessage();
    }
}
