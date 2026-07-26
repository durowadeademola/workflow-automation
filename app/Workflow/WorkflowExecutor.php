<?php

namespace App\Workflow;

use App\Models\AutomationWorkflow;
use App\Models\AutomationWorkflowRun;
use App\Models\AutomationWorkflowRunStep;
use Illuminate\Support\Facades\Log;
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
            $startedAt = microtime(true);

            try {
                $handler = $this->registry->resolve($step->type);
                $output = $handler->execute($resolvedConfig, $context);
                $context->setStepOutput($step->key, $output);

                AutomationWorkflowRunStep::create([
                    'automation_workflow_run_id' => $run->id,
                    'automation_workflow_step_id' => $step->id,
                    'key' => $step->key,
                    'status' => 'completed',
                    'input' => $resolvedConfig,
                    'output' => $output,
                    'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                ]);
            } catch (Throwable $e) {
                AutomationWorkflowRunStep::create([
                    'automation_workflow_run_id' => $run->id,
                    'automation_workflow_step_id' => $step->id,
                    'key' => $step->key,
                    'status' => 'failed',
                    'input' => $resolvedConfig,
                    'error' => $e->getMessage(),
                    'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
                ]);

                Log::error('Workflow step failed', [
                    'workflow' => $workflow->slug,
                    'step' => $step->key,
                    'error' => $e->getMessage(),
                ]);

                $run->update([
                    'status' => 'failed',
                    'context' => $context->all(),
                    'error' => "Step [{$step->key}] failed: {$e->getMessage()}",
                    'completed_at' => now(),
                ]);

                return $run;
            }
        }

        $run->update([
            'status' => 'completed',
            'context' => $context->all(),
            'completed_at' => now(),
        ]);

        return $run;
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
