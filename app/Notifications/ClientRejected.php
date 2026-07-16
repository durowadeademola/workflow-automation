<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientRejected extends Notification
{
    public function __construct(protected Client $client) {}

    /**
     * Mail only — a rejected client can't log in (canAccessPanel() requires
     * status "active"), so a database notification would never be seen.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Update on your Blueflow application')
            ->greeting("Hi {$notifiable->name},")
            ->line("We've reviewed your business registration and are unable to approve it at this time.");

        if ($this->client->rejection_reason) {
            $message->line("Reason given: {$this->client->rejection_reason}");
        }

        return $message->line('If you believe this was a mistake or would like to provide more information, please get in touch.')
            ->action('Contact Us', url('/contact'));
    }
}
