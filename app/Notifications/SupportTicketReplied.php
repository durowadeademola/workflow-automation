<?php

namespace App\Notifications;

use App\Models\SupportTicket;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SupportTicketReplied extends Notification
{
    public function __construct(protected SupportTicket $ticket, protected string $replierName) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return $notifiable->email_notifications_enabled
            ? ['mail', 'database']
            : ['database'];
    }

    private function url(object $notifiable): string
    {
        $panel = $notifiable->is_admin ? 'admin' : 'user';

        return url("/{$panel}/support-tickets/{$this->ticket->id}");
    }

    public function toMail(object $notifiable): MailMessage
    {
        $ticket = $this->ticket;

        return (new MailMessage)
            ->subject("New reply on: {$ticket->subject}")
            ->greeting("Hi {$notifiable->name},")
            ->line("{$this->replierName} replied to the support ticket \"{$ticket->subject}\".")
            ->action('View Ticket', $this->url($notifiable));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $ticket = $this->ticket;

        return FilamentNotification::make()
            ->title('New reply on your ticket')
            ->body("{$this->replierName} replied to \"{$ticket->subject}\"")
            ->success()
            ->actions([
                Action::make('view')->button()->url($this->url($notifiable)),
            ])
            ->getDatabaseMessage();
    }
}
