<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycRejected extends Notification
{
    public function __construct(protected KycSubmission $submission) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your identity verification needs another look')
            ->greeting("Hi {$notifiable->name},")
            ->line('Your submitted KYC documents could not be approved for the following reason:')
            ->line($this->submission->rejection_reason ?? 'No reason was given.')
            ->action('Resubmit', url('/user/kyc-verification'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('KYC rejected')
            ->body($this->submission->rejection_reason ?? 'Your identity verification was rejected.')
            ->danger()
            ->getDatabaseMessage();
    }
}
