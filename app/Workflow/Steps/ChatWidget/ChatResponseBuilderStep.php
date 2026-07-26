<?php

namespace App\Workflow\Steps\ChatWidget;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * Ports n8n's "Respond to Widget" node: whichever dispatch branch actually
 * ran (or none, for a plain reply) already produced the right shape — this
 * just forwards it. Always runs, matching the priority order the extracted
 * markers are checked in (only one branch fires per turn in practice).
 */
class ChatResponseBuilderStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $extract = $context->get('steps.extract', []);

        if ($handoff = $context->get('steps.dispatch_handoff')) {
            return $handoff;
        }

        if ($appointment = $context->get('steps.dispatch_appointment')) {
            return $appointment;
        }

        if ($lead = $context->get('steps.dispatch_lead')) {
            return $lead;
        }

        if ($registration = $context->get('steps.dispatch_registration')) {
            return $registration;
        }

        return [
            'reply' => $extract['reply'] ?? '',
            'sourceUrl' => $extract['sourceUrl'] ?? '',
        ];
    }
}
