<?php

namespace App\Notifications;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadQualified extends Notification
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
            ->subject('A visitor showed real interest — worth a follow-up')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$customer->display_name} on your chat widget is interested in: {$customer->lead_intent}")
            ->when($customer->lead_budget, fn ($mail) => $mail->line("Budget mentioned: {$customer->lead_budget}"))
            ->when($customer->lead_timeline, fn ($mail) => $mail->line("Timeline mentioned: {$customer->lead_timeline}"))
            ->action('View Conversation', url('/user/customers'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $customer = $this->customer;

        return FilamentNotification::make()
            ->title('Qualified lead captured')
            ->body("{$customer->display_name} is interested in: {$customer->lead_intent}")
            ->success()
            ->actions([
                Action::make('view')->button()->url('/user/customers'),
            ])
            ->getDatabaseMessage();
    }
}
