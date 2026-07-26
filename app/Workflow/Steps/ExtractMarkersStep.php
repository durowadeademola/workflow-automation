<?php

namespace App\Workflow\Steps;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * Ports n8n's "Extract Reply" code node — same marker regexes, same
 * malformed-JSON-means-incomplete handling, same required-field checks
 * (lead needs intent; registration needs name + (email or phone)).
 */
class ExtractMarkersStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $reply = $config['text'] ?? '';

        $sourceUrl = '';
        if (preg_match('/SOURCE:(https?:\/\/\S+)/', $reply, $m)) {
            $sourceUrl = $m[1];
            $reply = trim(preg_replace('/\nSOURCE:https?:\/\/\S+/', '', $reply));
        }

        $wantsHandoff = false;
        if (preg_match('/HANDOFF:\s*REQUESTED/i', $reply)) {
            $wantsHandoff = true;
            $reply = trim(preg_replace('/\n?HANDOFF:\s*REQUESTED/i', '', $reply));
        }

        $wantsAppointment = false;
        $appointmentDetails = null;
        if (preg_match('/APPOINTMENT:BOOK\s*(\{[\s\S]*?\})/i', $reply, $m)) {
            $decoded = json_decode($m[1], true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $appointmentDetails = $decoded;
                $wantsAppointment = true;
            }
            $reply = trim(preg_replace('/\n?APPOINTMENT:BOOK\s*\{[\s\S]*?\}/i', '', $reply));
        }

        $wantsLeadCapture = false;
        $leadDetails = null;
        if (preg_match('/LEAD:QUALIFIED\s*(\{[\s\S]*?\})/i', $reply, $m)) {
            $decoded = json_decode($m[1], true);
            if (json_last_error() === JSON_ERROR_NONE && ! empty($decoded['intent'])) {
                $leadDetails = $decoded;
                $wantsLeadCapture = true;
            }
            $reply = trim(preg_replace('/\n?LEAD:QUALIFIED\s*\{[\s\S]*?\}/i', '', $reply));
        }

        $wantsRegistration = false;
        $registrationDetails = null;
        if (preg_match('/REGISTER:DETAILS\s*(\{[\s\S]*?\})/i', $reply, $m)) {
            $decoded = json_decode($m[1], true);
            if (json_last_error() === JSON_ERROR_NONE && ! empty($decoded['name']) && (! empty($decoded['email']) || ! empty($decoded['phone']))) {
                $registrationDetails = $decoded;
                $wantsRegistration = true;
            }
            $reply = trim(preg_replace('/\n?REGISTER:DETAILS\s*\{[\s\S]*?\}/i', '', $reply));
        }

        $transcript = array_merge(
            $context->get('trigger.history', []),
            [
                ['role' => 'user', 'content' => $context->get('trigger.message', '')],
                ['role' => 'assistant', 'content' => $reply],
            ]
        );

        return [
            'reply' => trim($reply),
            'sourceUrl' => $sourceUrl,
            'wantsHandoff' => $wantsHandoff,
            'wantsAppointment' => $wantsAppointment,
            'appointmentDetails' => $appointmentDetails,
            'wantsLeadCapture' => $wantsLeadCapture,
            'leadDetails' => $leadDetails,
            'wantsRegistration' => $wantsRegistration,
            'registrationDetails' => $registrationDetails,
            'transcript' => $transcript,
        ];
    }
}
