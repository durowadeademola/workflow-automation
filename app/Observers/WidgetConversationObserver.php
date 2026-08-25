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

        // No active agent could be matched — the business itself needs to
        // know a customer is waiting for a human. For a client with no
        // separate agent accounts (a single owner login is the common
        // case), this is otherwise the ONLY notification path this handoff
        // would ever get, since there's no assigned agent to tell. This is
        // entirely the client's concern, not Blueflow's own admins'.
        $clientUsers = User::where('client_id', $conversation->client_id)
            ->where('is_client', true)
            ->get();

        if ($clientUsers->isNotEmpty()) {
            Notification::send($clientUsers, new HandoffRequested($conversation));
        }
    }
}
