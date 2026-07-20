<?php

namespace App\Notifications;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomerRegistered extends Notification
{
    public function __construct(protected Customer $customer) {}

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
        $customer = $this->customer;

        return (new MailMessage)
            ->subject('A visitor left their details on your chat widget')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$customer->display_name} registered their details on your chat widget.")
            ->when($customer->email, fn ($mail) => $mail->line("Email: {$customer->email}"))
            ->when($customer->phone, fn ($mail) => $mail->line("Phone: {$customer->phone}"))
            ->when($customer->lead_intent, fn ($mail) => $mail->line("Interested in: {$customer->lead_intent}"))
            ->action('View Customer', url('/user/customers'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $customer = $this->customer;

        return FilamentNotification::make()
            ->title('New visitor registered their details')
            ->body("{$customer->display_name} left their contact details".($customer->lead_intent ? " — interested in: {$customer->lead_intent}" : ''))
            ->success()
            ->actions([
                Action::make('view')->button()->url('/user/customers'),
            ])
            ->getDatabaseMessage();
    }
}
