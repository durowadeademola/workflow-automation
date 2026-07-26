<?php

namespace App\Workflow\Steps\ChatWidget;

use App\Http\Controllers\API\WidgetAppointmentController;
use App\Workflow\Contracts\StepHandler;
use App\Workflow\Steps\Concerns\CallsControllerInternally;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Book Appointment" HTTP node + "Build Appointment Response"
 * code node — same status branching (success/conflict/limit_reached/
 * unavailable), same visitor-facing copy for each.
 */
class DispatchAppointmentStep implements StepHandler
{
    use CallsControllerInternally;

    public function execute(array $config, WorkflowContext $context): array
    {
        $extract = $context->get('steps.extract', []);
        $details = $extract['appointmentDetails'] ?? [];

        try {
            $result = $this->callController(WidgetAppointmentController::class, 'store', [
                'client_id' => $context->get('trigger.clientId'),
                'session_token' => $context->get('trigger.sessionToken'),
                'name' => $details['name'] ?? null,
                'scheduled_at' => $details['scheduled_at'] ?? null,
                'reason' => $details['reason'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Native workflow: appointment dispatch failed', ['error' => $e->getMessage()]);

            return [
                'reply' => "Sorry, we can't book appointments online right now - please reach out to us directly and we'll get this sorted.",
                'sourceUrl' => $extract['sourceUrl'] ?? '',
                'appointmentStatus' => 'unavailable',
            ];
        }

        $body = $result['body'];
        $status = $body['status'] ?? null;
        $priorReply = $extract['reply'] ?? '';

        $reply = match ($status) {
            'success' => trim($priorReply . "\n\nYou're all set for {$body['data']['scheduled_at_human']}. We look forward to seeing you!"),
            'conflict' => 'That time slot is already booked for someone else - could you give me a different date or time?',
            'limit_reached' => "We've reached our appointment booking limit for right now, so a team member will need to book this one manually - don't worry, you won't be charged twice for it.",
            'unavailable' => "Sorry, we can't book appointments online right now - please reach out to us directly and we'll get this sorted.",
            default => $body['message'] ?? $priorReply,
        };

        return [
            'reply' => trim($reply),
            'sourceUrl' => $extract['sourceUrl'] ?? '',
            'appointmentStatus' => $status,
        ];
    }
}
