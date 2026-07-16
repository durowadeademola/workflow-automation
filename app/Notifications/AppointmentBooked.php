<?php

namespace App\Notifications;

use App\Models\Appointment;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppointmentBooked extends Notification
{
    public function __construct(protected Appointment $appointment) {}

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
        $appointment = $this->appointment;

        return (new MailMessage)
            ->subject('New appointment booked')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$appointment->name} just booked an appointment via your chat widget.")
            ->line('When: '.$appointment->scheduled_at->format('l, F j, Y \a\t g:i A'))
            ->when($appointment->reason, fn ($mail) => $mail->line("Reason: {$appointment->reason}"))
            ->when($appointment->email, fn ($mail) => $mail->line("Contact email: {$appointment->email}"))
            ->when($appointment->phone, fn ($mail) => $mail->line("Contact phone: {$appointment->phone}"))
            ->action('View Appointment', url('/user/appointments'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $appointment = $this->appointment;

        return FilamentNotification::make()
            ->title('New appointment booked')
            ->body("{$appointment->name} — ".$appointment->scheduled_at->format('M j, Y \a\t g:i A'))
            ->success()
            ->actions([
                Action::make('view')->button()->url('/user/appointments'),
            ])
            ->getDatabaseMessage();
    }
}
