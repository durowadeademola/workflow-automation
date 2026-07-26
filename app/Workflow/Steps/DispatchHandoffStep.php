<?php

namespace App\Workflow\Steps;

use App\Http\Controllers\API\WidgetConversationController;
use App\Workflow\Contracts\StepHandler;
use App\Workflow\Steps\Concerns\CallsControllerInternally;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Request Human Handoff" HTTP node + "Build Handoff Response"
 * code node — calls WidgetConversationController::store() directly instead
 * of POSTing to /api/widget/conversations.
 */
class DispatchHandoffStep implements StepHandler
{
    use CallsControllerInternally;

    public function execute(array $config, WorkflowContext $context): array
    {
        $extract = $context->get('steps.extract', []);

        try {
            $result = $this->callController(WidgetConversationController::class, 'store', [
                'client_id' => $context->get('trigger.clientId'),
                'session_token' => $context->get('trigger.sessionToken'),
                'visitor_name' => null,
                'transcript' => $extract['transcript'] ?? [],
            ]);
        } catch (\Throwable $e) {
            Log::warning('Native workflow: handoff dispatch failed', ['error' => $e->getMessage()]);

            return [
                'reply' => $extract['reply'] ?? "One moment - I'm connecting you with a member of our team now.",
                'sourceUrl' => $extract['sourceUrl'] ?? '',
                'handoff' => true,
                'conversationId' => null,
                'lastMessageId' => null,
            ];
        }

        $data = $result['body']['data'] ?? $result['body'];

        return [
            'reply' => $extract['reply'] ?: "One moment - I'm connecting you with a member of our team now.",
            'sourceUrl' => $extract['sourceUrl'] ?? '',
            'handoff' => true,
            'conversationId' => $data['conversation_id'] ?? null,
            'lastMessageId' => $data['last_message_id'] ?? null,
        ];
    }
}
