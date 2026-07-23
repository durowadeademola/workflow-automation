<?php

namespace App\Notifications;

use App\Models\Lead;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewLeadReceived extends Notification
{
    public function __construct(protected Lead $lead) {}

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
        $lead = $this->lead;

        return (new MailMessage)
            ->subject('New lead received')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$lead->name}".($lead->business_name ? " ({$lead->business_name})" : '')." submitted the contact form.")
            ->when($lead->email, fn ($mail) => $mail->line("Email: {$lead->email}"))
            ->when($lead->phone, fn ($mail) => $mail->line("Phone: {$lead->phone}"))
            ->when($lead->interest, fn ($mail) => $mail->line("Interested in: {$lead->interest}"))
            ->when($lead->message, fn ($mail) => $mail->line("Message: \"{$lead->message}\""))
            ->action('View Lead', url("/admin/leads/{$lead->id}/edit"));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Lead Received')
            ->success()
            ->body("{$this->lead->name} submitted the contact form.")
            ->actions([
                Action::make('view')->button()->url("/admin/leads/{$this->lead->id}/edit"),
            ])
            ->getDatabaseMessage();
    }
}
