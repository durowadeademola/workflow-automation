<?php

namespace App\Workflow\Steps\ChatWidget;

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
            // `interest` (the reason they're reaching out) is required here,
            // not just name + a contact method — a deliberate change beyond
            // the literal n8n port, since the reason is now a required part
            // of the registration prompt instruction too (see
            // ChatPromptBuilderStep).
            if (json_last_error() === JSON_ERROR_NONE
                && ! empty($decoded['name'])
                && (! empty($decoded['email']) || ! empty($decoded['phone']))
                && ! empty($decoded['interest'])) {
                $registrationDetails = $decoded;
                $wantsRegistration = true;
            }
            $reply = trim(preg_replace('/\n?REGISTER:DETAILS\s*\{[\s\S]*?\}/i', '', $reply));
        }

        // Fallback for a model that emits a marker's JSON payload but drops
        // the literal label text it's supposed to sit behind (observed for
        // real with openai/gpt-oss-120b dropping "LEAD:QUALIFIED" while
        // still producing valid JSON) — without this, none of the strict
        // regexes above match, so the raw JSON leaks straight into the
        // customer-facing reply AND the lead/appointment/registration is
        // silently never saved. Only looks at a bare JSON object sitting on
        // its own trailing line (never mid-sentence prose), and routes it
        // by shape using each marker's own distinguishing field(s) —
        // 'scheduled_at' only ever appears on an appointment, 'interest'
        // alongside a contact method only on a registration, 'intent' only
        // on a lead — so a normal reply that happens to end on an unrelated
        // sentence is never mistaken for one of these.
        if (preg_match('/\n?(\{[^{}]*\})\s*$/', $reply, $m)) {
            $decoded = json_decode($m[1], true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                if (! $wantsAppointment && isset($decoded['scheduled_at'])) {
                    $appointmentDetails = $decoded;
                    $wantsAppointment = true;
                    $reply = trim(substr($reply, 0, -strlen($m[0])));
                } elseif (! $wantsRegistration
                    && ! empty($decoded['name'])
                    && (! empty($decoded['email']) || ! empty($decoded['phone']))
                    && ! empty($decoded['interest'])) {
                    $registrationDetails = $decoded;
                    $wantsRegistration = true;
                    $reply = trim(substr($reply, 0, -strlen($m[0])));
                } elseif (! $wantsLeadCapture && ! empty($decoded['intent'])) {
                    $leadDetails = $decoded;
                    $wantsLeadCapture = true;
                    $reply = trim(substr($reply, 0, -strlen($m[0])));
                }
            }
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
