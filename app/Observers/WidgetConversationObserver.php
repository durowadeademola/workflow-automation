<?php

namespace App\Observers;

use App\Models\User;
use App\Models\WidgetConversation;
use App\Notifications\HandoffRequested;
use Illuminate\Support\Facades\Notification;

class WidgetConversationObserver
{
    /**
     * Handle the WidgetConversation "created" event. A conversation is only
     * ever created as a direct result of a visitor asking to speak with a
     * human, so every new row here is a fresh handoff request.
     */
    public function created(WidgetConversation $conversation): void
    {
        if ($conversation->status !== 'waiting') {
            return;
        }

        $agents = User::where('client_id', $conversation->client_id)
            ->where('is_agent', true)
            ->get();

        if ($agents->isEmpty()) {
            return;
        }

        Notification::send($agents, new HandoffRequested($conversation));
    }
}
