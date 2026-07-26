<?php

namespace App\Workflow;

use App\Models\AutomationWorkflow;
use App\Models\AutomationWorkflowRun;
use App\Models\AutomationWorkflowRunStep;
use App\Models\AutomationWorkflowStep;
use App\Models\User;
use App\Notifications\WorkflowRunFailedNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Runs a workflow's steps in order against a trigger payload, persisting a
 * full run + per-step log as it goes. This is the one piece every workflow
 * (chat replies today, anything else later) shares — everything specific to
 * what a workflow actually does lives in its steps, not here.
 */
class WorkflowExecutor
{
    public function __construct(private StepRegistry $registry) {}

    public function run(AutomationWorkflow $workflow, array $triggerPayload): AutomationWorkflowRun
    {
        $context = new WorkflowContext($triggerPayload);

        $run = AutomationWorkflowRun::create([
            'automation_workflow_id' => $workflow->id,
            'status' => 'running',
            'trigger_payload' => $triggerPayload,
            'started_at' => now(),
        ]);

        foreach ($workflow->steps as $step) {
            if (! $this->shouldRun($step->run_if, $context)) {
                AutomationWorkflowRunStep::create([
                    'automation_workflow_run_id' => $run->id,
                    'automation_workflow_step_id' => $step->id,
                    'key' => $step->key,
                    'status' => 'skipped',
                ]);

                continue;
            }

            $resolvedConfig = $context->resolve($step->config ?? []);

            $result = $this->runStepWithRetries($step, $resolvedConfig, $context);

            AutomationWorkflowRunStep::create([
                'automation_workflow_run_id' => $run->id,
                'automation_workflow_step_id' => $step->id,
                'key' => $step->key,
                'status' => $result['success'] ? 'completed' : 'failed',
                'attempts' => $result['attempts'],
                'input' => $resolvedConfig,
                'output' => $result['output'],
                'error' => $result['error'],
                'duration_ms' => $result['duration_ms'],
            ]);

            if (! $result['success']) {
                $this->failRun(
                    $run,
                    $context,
                    "Step [{$step->key}] failed after {$result['attempts']} attempt(s): {$result['error']}"
                );

                return $run;
            }

            $context->setStepOutput($step->key, $result['output']);
        }

        $run->update([
            'status' => 'completed',
            'context' => $context->all(),
            'completed_at' => now(),
        ]);

        return $run;
    }

    /**
     * Retries only kick in if a step is explicitly configured for more than
     * one attempt (max_attempts > 1, default 1 — no retry, unchanged
     * behaviour for every step that doesn't opt in). Blind automatic
     * retries on every step would be unsafe: a step with a real side effect
     * (booking an appointment, inserting a row) could double it if the
     * first attempt actually succeeded server-side but the response never
     * made it back.
     *
     * @return array{success: bool, output: ?array, error: ?string, attempts: int, duration_ms: int}
     */
    private function runStepWithRetries(AutomationWorkflowStep $step, array $resolvedConfig, WorkflowContext $context): array
    {
        $maxAttempts = max(1, $step->max_attempts);
        $retryDelayMs = max(0, $step->retry_delay_ms);
        $startedAt = microtime(true);
        $lastError = null;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $handler = $this->registry->resolve($step->type);
                $output = $handler->execute($resolvedConfig, $context);

                return [
                    'success' => true,
                    'output' => $output,
                    'error' => null,
                    'attempts' => $attempt,
                    'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                ];
            } catch (Throwable $e) {
                $lastError = $e->getMessage();

                Log::warning('Workflow step attempt failed', [
                    'step' => $step->key,
                    'attempt' => $attempt,
                    'max_attempts' => $maxAttempts,
                    'error' => $lastError,
                ]);

                if ($attempt < $maxAttempts && $retryDelayMs > 0) {
                    usleep($retryDelayMs * 1000);
                }
            }
        }

        return [
            'success' => false,
            'output' => null,
            'error' => $lastError,
            'attempts' => $maxAttempts,
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
        ];
    }

    private function failRun(AutomationWorkflowRun $run, WorkflowContext $context, string $error): void
    {
        Log::error('Workflow run failed', [
            'workflow_run_id' => $run->id,
            'error' => $error,
        ]);

        $run->update([
            'status' => 'failed',
            'context' => $context->all(),
            'error' => $error,
            'completed_at' => now(),
        ]);

        // The alert is best-effort — if sending it fails too (e.g. a mail
        // outage), that must never surface as if the workflow run itself
        // failed differently, or mask the real error above with an
        // unrelated one about the notification.
        try {
            $admins = User::where('is_admin', true)->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new WorkflowRunFailedNotification($run->fresh()));
            }
        } catch (Throwable $e) {
            Log::error('Failed to send workflow-run-failed alert', [
                'workflow_run_id' => $run->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function shouldRun(?array $runIf, WorkflowContext $context): bool
    {
        if (! $runIf) {
            return true;
        }

        $actual = $context->get($runIf['field']);

        if (array_key_exists('equals', $runIf)) {
            return $actual == $runIf['equals'];
        }

        return (bool) $actual;
    }
}
