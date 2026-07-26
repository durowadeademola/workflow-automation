<?php

namespace App\Workflow\Steps;

use App\Http\Controllers\API\WidgetRegistrationController;
use App\Workflow\Contracts\StepHandler;
use App\Workflow\Steps\Concerns\CallsControllerInternally;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Capture Registration" HTTP node + "Build Registration
 * Response" code node. Silent CRM save — the AI already confirmed the
 * details back to the visitor in its own reply text.
 */
class DispatchRegistrationStep implements StepHandler
{
    use CallsControllerInternally;

    public function execute(array $config, WorkflowContext $context): array
    {
        $extract = $context->get('steps.extract', []);
        $details = $extract['registrationDetails'] ?? [];

        try {
            $this->callController(WidgetRegistrationController::class, 'store', [
                'client_id' => $context->get('trigger.clientId'),
                'session_token' => $context->get('trigger.sessionToken'),
                'name' => $details['name'] ?? null,
                'email' => $details['email'] ?? null,
                'phone' => $details['phone'] ?? null,
                'interest' => $details['interest'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Native workflow: registration dispatch failed', ['error' => $e->getMessage()]);
        }

        return [
            'reply' => $extract['reply'] ?? '',
            'sourceUrl' => $extract['sourceUrl'] ?? '',
        ];
    }
}
