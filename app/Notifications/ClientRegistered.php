<?php

namespace App\Notifications;

use App\Models\Client;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent once, immediately after a business self-registers — separate from
 * ClientAwaitingApproval (which tells admins a business is waiting) and
 * ClientApproved (which unlocks their login once approved). Mail only: the
 * account is still "pending" at this point, so canAccessPanel() blocks
 * login and a database notification would never be seen.
 */
class ClientRegistered extends Notification
{
    public function __construct(protected Client $client) {}

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
            ->subject('Welcome to Blueflow — your application is in review')
            ->greeting("Hi {$notifiable->name},")
            ->line("Thanks for signing up with Blueflow for {$this->client->name}!")
            ->line("We're reviewing your application now — this usually only takes a short while. We'll email you the moment you're approved and able to log in.")
            ->line('In the meantime, feel free to reach out if you have any questions.');
    }
}
