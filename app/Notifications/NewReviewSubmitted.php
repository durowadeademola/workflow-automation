<?php

namespace App\Notifications;

use App\Models\Review;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewReviewSubmitted extends Notification
{
    public function __construct(protected Review $review) {}

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
        $review = $this->review;

        return (new MailMessage)
            ->subject('New review submitted')
            ->greeting("Hi {$notifiable->name},")
            ->line("{$review->name} ({$review->client?->name}) left a {$review->rating}-star review.")
            ->line("\"{$review->description}\"")
            ->action('Review Submission', url('/admin/reviews'));
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $review = $this->review;

        return FilamentNotification::make()
            ->title('New review submitted')
            ->body("{$review->name} — {$review->rating} stars")
            ->success()
            ->actions([
                Action::make('view')->button()->url('/admin/reviews'),
            ])
            ->getDatabaseMessage();
    }
}
