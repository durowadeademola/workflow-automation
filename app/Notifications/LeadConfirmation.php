<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to the person who just submitted the contact/lead form — not a User
 * account, so unlike every other notification in the app this always goes
 * out (no email_notifications_enabled to check) and mail is the only
 * channel that makes sense; a lead never logs in to see a database one.
 */
class LeadConfirmation extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("We've received your request")
            ->greeting("Hi {$notifiable->name},")
            ->line("Thanks for reaching out to Blueflow".($notifiable->interest ? " about {$notifiable->interest}" : '').'!')
            ->line("We've received your request and will get back to you within one business day.")
            ->line("In the meantime, if it's urgent, feel free to message us directly on WhatsApp.")
            ->action('Message Us on WhatsApp', 'https://wa.me/2347064706193');
    }
}
