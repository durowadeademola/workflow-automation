<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSupportTicket extends Notification
{
    public function __construct(protected SupportTicket $ticket) {}

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
        $ticket = $this->ticket;

        return (new MailMessage)
            ->subject("New support ticket: {$ticket->subject}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$ticket->client?->name} opened a new support ticket.")
            ->line("Subject: {$ticket->subject}")
            ->action('View Ticket', url('/admin/support-tickets/'.$ticket->id));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $ticket = $this->ticket;

        return FilamentNotification::make()
            ->title('New support ticket')
            ->body("{$ticket->client?->name} — {$ticket->subject}")
            ->warning()
            ->actions([
                Action::make('view')->button()->url('/admin/support-tickets/'.$ticket->id),
            ])
            ->getDatabaseMessage();
    }
}
