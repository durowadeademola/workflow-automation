<?php

namespace App\Services;

use App\Models\Client;
use App\Models\User;
use App\Notifications\MessageLimitReached;
use Illuminate\Support\Facades\Notification;

class MessageLimitService
{
    /**
     * Fires once per billing period the first time a client's widget hits
     * its plan's message cap — guarded by a timestamp on the subscription
     * itself so a flood of blocked requests doesn't send a flood of emails.
     */
    public function notifyOnceIfLimitReached(Client $client): void
    {
        $subscription = $client->currentSubscription();

        if (! $subscription || $subscription->limit_reached_notified_at) {
            return;
        }

        $subscription->update(['limit_reached_notified_at' => now()]);

        $recipients = User::where('client_id', $client->id)
            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new MessageLimitReached());
        }
    }
}
