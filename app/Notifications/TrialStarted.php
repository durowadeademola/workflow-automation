<?php

namespace App\Notifications;

use App\Models\Subscription;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TrialStarted extends Notification
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
        $service = $this->subscription->service ?? 'chat-widget';
        $label = $this->subscription->serviceLabel();

        $intro = $service === 'marketing-automation'
            ? 'Your free trial is live — you can start building customer journeys and sending marketing emails right away, no payment needed yet.'
            : 'Your free trial is live — your chat widget can start answering visitors right away, no payment needed yet.';

        [$actionLabel, $actionUrl] = $service === 'marketing-automation'
            ? ['Set Up a Journey', url('/user/marketing/marketing-journeys')]
            : ['Set Up Your Widget', url('/user/widget-settings')];

        return (new MailMessage)
            ->subject("Your 14-day {$label} free trial has started")
            ->greeting("Hi {$notifiable->name},")
            ->line($intro)
            ->line('Trial ends: '.$this->subscription->end_date->format('l, F j, Y'))
            ->action($actionLabel, $actionUrl)
            ->line("We'll remind you before it ends, and you can subscribe to a plan any time from Billing to keep it running afterward.");
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title("Your {$this->subscription->serviceLabel()} free trial has started")
            ->body('Ends '.$this->subscription->end_date->format('M j, Y').'.')
            ->success()
            ->getDatabaseMessage();
    }
}
