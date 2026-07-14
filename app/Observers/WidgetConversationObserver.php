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
     * human, so every new row here is a fresh handoff request. By this
     * point `AgentAssignmentService` has already matched it to whichever
     * eligible agent has the lightest load — we just notify that match.
     */
    public function created(WidgetConversation $conversation): void
    {
        if ($conversation->status !== 'waiting') {
            return;
        }

        if ($conversation->agent_id) {
            $agent = User::find($conversation->agent_id);

            if ($agent) {
                Notification::send($agent, new HandoffRequested($conversation));
            }

            return;
        }

        // No active agent could be matched — let admins know so they can
        // staff up or step in themselves, rather than the request going
        // completely unnoticed.
        $admins = User::where('is_admin', true)->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new HandoffRequested($conversation));
        }
    }
}
