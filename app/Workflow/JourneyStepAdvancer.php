<?php

namespace App\Workflow;

use App\Models\AutomationWorkflowEnrollment;
use App\Models\AutomationWorkflowEnrollmentSend;
use App\Models\AutomationWorkflowStep;
use App\Models\Customer;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Throwable;

/**
 * Advances one AutomationWorkflowEnrollment by exactly one due step. Not a
 * replacement for WorkflowExecutor — that class runs every step of a
 * workflow synchronously in one call and has no notion of pausing between
 * steps. A journey needs to persist across hours/days, so each tick (see
 * the AdvanceMarketingJourneys command) advances one enrollment by one step
 * at a time, reusing the same StepHandler/StepRegistry/WorkflowContext
 * primitives WorkflowExecutor uses, just driven by a different clock.
 *
 * `current_step_order` on the enrollment is a zero-based position in the
 * workflow's ordered steps() collection (not the raw `order` column value
 * — steps() is already sorted by it, and treating gaps/duplicates in that
 * column as index arithmetic would be fragile).
 */
class JourneyStepAdvancer
{
    public function __construct(private StepRegistry $stepRegistry) {}

    public function advance(AutomationWorkflowEnrollment $enrollment): void
    {
        $enrollment->loadMissing('workflow.steps', 'customer', 'client');

        $steps = $enrollment->workflow->steps->values();
        $currentStep = $steps->get($enrollment->current_step_order);

        if (! $currentStep) {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);

            return;
        }

        $customer = $enrollment->customer;

        if (! $customer) {
            $enrollment->update([
                'status' => 'exited',
                'exited_at' => now(),
                'exit_reason' => 'customer_deleted',
            ]);

            return;
        }

        $basePayload = $this->buildTriggerPayload($enrollment, $customer);
        $conditionContext = new WorkflowContext($basePayload);

        if ($this->conditionMet($currentStep->exit_if, $conditionContext)) {
            $enrollment->update([
                'status' => 'exited',
                'exited_at' => now(),
                'exit_reason' => 'exit_condition_met',
                'context' => $basePayload,
            ]);

            return;
        }

        $emailLimitReached = $currentStep->channel === 'email'
            && $enrollment->client->hasReachedEmailSendLimit();

        $trackingToken = Str::random(40);

        $send = AutomationWorkflowEnrollmentSend::create([
            'enrollment_id' => $enrollment->id,
            'step_key' => $currentStep->key,
            'channel' => $currentStep->channel,
            'status' => $emailLimitReached ? 'skipped' : 'pending',
            'note' => $emailLimitReached ? 'limit_reached' : null,
            'tracking_token' => $trackingToken,
        ]);

        $payload = $basePayload;

        if (! $emailLimitReached) {
            $payload = $this->buildTriggerPayload($enrollment, $customer, $trackingToken);
            $context = new WorkflowContext($payload);
            $resolvedConfig = $context->resolve($currentStep->config ?? []);

            try {
                $result = $this->stepRegistry->resolve($currentStep->type)->execute($resolvedConfig, $context);
            } catch (Throwable $e) {
                $result = ['status' => 'failed', 'reason' => $e->getMessage()];
            }

            $status = $result['status'] ?? 'failed';

            $send->update([
                'status' => $status,
                'note' => $result['reason'] ?? null,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);

            if ($status === 'failed') {
                // Terminal, same philosophy as WorkflowExecutor::failRun() —
                // a failure isn't silently retried forever.
                $enrollment->update([
                    'status' => 'failed',
                    'context' => $payload,
                ]);

                return;
            }
        }

        $this->advanceToNextStep($enrollment, $steps, $payload);
    }

    private function advanceToNextStep(AutomationWorkflowEnrollment $enrollment, $steps, array $context): void
    {
        $nextIndex = $enrollment->current_step_order + 1;
        $nextStep = $steps->get($nextIndex);

        if (! $nextStep) {
            $enrollment->update([
                'status' => 'completed',
                'completed_at' => now(),
                'context' => $context,
            ]);

            return;
        }

        $enrollment->update([
            'current_step_order' => $nextIndex,
            'next_run_at' => $this->computeNextRunAt($nextStep),
            'context' => $context,
        ]);
    }

    private function computeNextRunAt(AutomationWorkflowStep $step): Carbon
    {
        return match ($step->wait_unit) {
            'minutes' => now()->addMinutes($step->wait_amount),
            'days' => now()->addDays($step->wait_amount),
            default => now()->addHours($step->wait_amount),
        };
    }

    /**
     * Same tiny {field, equals} check WorkflowExecutor::shouldRun() uses for
     * run_if, just used for the opposite purpose here: a truthy match means
     * "exit the journey", not "run this step".
     */
    private function conditionMet(?array $condition, WorkflowContext $context): bool
    {
        if (! $condition) {
            return false;
        }

        $actual = $context->get($condition['field'] ?? '');

        if (array_key_exists('equals', $condition)) {
            return $actual == $condition['equals'];
        }

        return (bool) $actual;
    }

    private function buildTriggerPayload(
        AutomationWorkflowEnrollment $enrollment,
        Customer $customer,
        ?string $trackingToken = null,
    ): array {
        $latestAppointment = $customer->appointments()->latest('scheduled_at')->first();

        return array_merge($enrollment->context ?? [], [
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name ?: $customer->display_name,
                'email' => $customer->email,
                'phone' => $customer->phone,
                'leadIntent' => $customer->lead_intent,
                'isQualifiedLead' => (bool) $customer->is_qualified_lead,
                'subscribedToMarketing' => (bool) $customer->subscribed_to_marketing,
                'registeredAt' => optional($customer->registered_at)->toIso8601String(),
            ],
            'latestAppointment' => $latestAppointment ? [
                'scheduledAt' => optional($latestAppointment->scheduled_at)->toIso8601String(),
                'reason' => $latestAppointment->reason,
                'status' => $latestAppointment->status,
            ] : null,
            'client' => [
                'id' => $enrollment->client->id,
                'name' => $enrollment->client->name,
            ],
            'send' => $trackingToken ? ['trackingToken' => $trackingToken] : null,
        ]);
    }
}
