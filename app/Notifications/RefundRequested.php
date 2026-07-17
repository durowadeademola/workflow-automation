<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RefundRequested extends Notification
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
            ->subject('A client requested a refund')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$subscription->client?->name} cancelled their {$subscription->name} subscription and requested a refund for their unused time.")
            ->line('Refund amount: ₦'.number_format($subscription->refund_amount))
            ->when($subscription->cancellation_reason, fn ($mail) => $mail->line("Reason given: {$subscription->cancellation_reason}"))
            ->action('Review Refund Request', url('/admin/subscriptions'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $subscription = $this->subscription;

        return FilamentNotification::make()
            ->title('Refund requested')
            ->body("{$subscription->client?->name} — ₦".number_format($subscription->refund_amount))
            ->warning()
            ->actions([
                Action::make('view')->button()->url('/admin/subscriptions'),
            ])
            ->getDatabaseMessage();
    }
}
