<?php

namespace App\Notifications;

use App\Models\Subscription;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionInvoice extends Notification
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

        $invoicePdf = Pdf::loadView('invoices.subscription', ['subscription' => $subscription])->output();
        $receiptPdf = Pdf::loadView('invoices.receipt', ['subscription' => $subscription])->output();

        return (new MailMessage)
            ->subject("Invoice and Receipt for your {$subscription->serviceLabel()} {$subscription->name} subscription")
            ->greeting("Hi {$notifiable->name},")
            ->line("Thanks for your payment — your {$subscription->serviceLabel()} {$subscription->name} subscription is now active.")
            ->line('Amount paid: ₦'.number_format($subscription->amount))
            ->line('Your invoice and payment receipt are attached for your records.')
            ->action('View Billing', url('/user/billing'))
            ->attachData($invoicePdf, "invoice-{$subscription->paystack_reference}.pdf", [
                'mime' => 'application/pdf',
            ])
            ->attachData($receiptPdf, "receipt-{$subscription->paystack_reference}.pdf", [
                'mime' => 'application/pdf',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $subscription = $this->subscription;

        return FilamentNotification::make()
            ->title('Payment received')
            ->body("Your {$subscription->serviceLabel()} {$subscription->name} subscription is now active. An invoice has been sent to your email.")
            ->success()
            ->getDatabaseMessage();
    }
}
