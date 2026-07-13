<?php

namespace App\Notifications;

use App\Models\WidgetConversation;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class HandoffRequested extends Notification
{
    public function __construct(protected WidgetConversation $conversation) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $visitor = $this->conversation->visitor_name ?: 'A visitor';

        return (new MailMessage)
            ->subject('A visitor wants to speak with you')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$visitor} on your live chat widget has asked to speak with a person.")
            ->action('Open Live Chat', url('/admin/live-chat'))
            ->line("Reply as soon as you can — they're waiting.");
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $visitor = $this->conversation->visitor_name ?: 'A visitor';

        return FilamentNotification::make()
            ->title('Live chat handoff requested')
            ->body("{$visitor} wants to talk to a human.")
            ->warning()
            ->actions([
                Action::make('view')
                    ->button()
                    ->url('/admin/live-chat')
                    ->label('Open Live Chat'),
            ])
            ->getDatabaseMessage();
    }
}
