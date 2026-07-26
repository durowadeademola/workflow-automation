<?php

namespace App\Workflow\Steps;

use App\Http\Controllers\API\WidgetLeadController;
use App\Workflow\Contracts\StepHandler;
use App\Workflow\Steps\Concerns\CallsControllerInternally;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Capture Lead" HTTP node + "Build Lead Response" code node.
 * Silent CRM enrichment — never changes what the visitor sees, success or
 * failure alike.
 */
class DispatchLeadStep implements StepHandler
{
    use CallsControllerInternally;

    public function execute(array $config, WorkflowContext $context): array
    {
        $extract = $context->get('steps.extract', []);
        $details = $extract['leadDetails'] ?? [];

        try {
            $this->callController(WidgetLeadController::class, 'store', [
                'client_id' => $context->get('trigger.clientId'),
                'session_token' => $context->get('trigger.sessionToken'),
                'intent' => $details['intent'] ?? null,
                'budget' => $details['budget'] ?? null,
                'timeline' => $details['timeline'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Native workflow: lead dispatch failed', ['error' => $e->getMessage()]);
        }

        return [
            'reply' => $extract['reply'] ?? '',
            'sourceUrl' => $extract['sourceUrl'] ?? '',
        ];
    }
}
