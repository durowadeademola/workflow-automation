<?php

namespace App\Console\Commands;

use App\Models\AutomationWorkflow;
use Illuminate\Console\Command;

/**
 * On-demand execution visibility until there's a real admin UI for it —
 * `php artisan workflows:runs` lists every workflow, `workflows:runs {slug}`
 * lists its recent runs, and `workflows:runs {slug} --run=5` drills into one
 * run's full step-by-step input/output/error.
 */
class ShowWorkflowRuns extends Command
{
    protected $signature = 'workflows:runs {slug? : Workflow slug to inspect} {--run= : Show full detail for a specific run ID} {--limit=20 : How many recent runs to list}';

    protected $description = 'Inspect AutomationWorkflow execution history from the console.';

    public function handle(): int
    {
        $slug = $this->argument('slug');

        if (! $slug) {
            return $this->listWorkflows();
        }

        $workflow = AutomationWorkflow::where('slug', $slug)->first();

        if (! $workflow) {
            $this->error("No workflow found with slug [{$slug}].");

            return self::FAILURE;
        }

        if ($runId = $this->option('run')) {
            return $this->showRun($workflow, (int) $runId);
        }

        return $this->listRuns($workflow);
    }

    private function listWorkflows(): int
    {
        $workflows = AutomationWorkflow::withCount('runs')->get();

        if ($workflows->isEmpty()) {
            $this->info('No workflows defined yet.');

            return self::SUCCESS;
        }

        $this->table(
            ['Slug', 'Name', 'Trigger', 'Active', 'Runs', 'Last Triggered'],
            $workflows->map(fn ($w) => [
                $w->slug,
                $w->name,
                $w->trigger_type,
                $w->is_active ? 'yes' : 'no',
                $w->runs_count,
                $w->last_triggered_at?->diffForHumans() ?? 'never',
            ])
        );

        $this->line('');
        $this->line('Run `workflows:runs {slug}` to see a workflow\'s recent executions.');

        return self::SUCCESS;
    }

    private function listRuns(AutomationWorkflow $workflow): int
    {
        $runs = $workflow->runs()->latest()->limit((int) $this->option('limit'))->get();

        if ($runs->isEmpty()) {
            $this->info("No runs yet for [{$workflow->slug}].");

            return self::SUCCESS;
        }

        $this->table(
            ['ID', 'Status', 'Started', 'Duration', 'Error'],
            $runs->map(fn ($run) => [
                $run->id,
                $run->status,
                $run->started_at?->format('Y-m-d H:i:s'),
                $run->completed_at && $run->started_at
                    ? $run->started_at->diffInSeconds($run->completed_at) . 's'
                    : '-',
                $run->error ? str($run->error)->limit(60) : '',
            ])
        );

        $this->line('');
        $this->line("Run `workflows:runs {$workflow->slug} --run=<id>` to see a run's full step-by-step detail.");

        return self::SUCCESS;
    }

    private function showRun(AutomationWorkflow $workflow, int $runId): int
    {
        $run = $workflow->runs()->where('id', $runId)->first();

        if (! $run) {
            $this->error("No run #{$runId} found for workflow [{$workflow->slug}].");

            return self::FAILURE;
        }

        $this->line("<fg=cyan>Run #{$run->id}</> — {$workflow->slug} — <fg=yellow>{$run->status}</>");
        $this->line("Started:   {$run->started_at}");
        $this->line("Completed: " . ($run->completed_at ?? '-'));

        if ($run->error) {
            $this->line("<fg=red>Error: {$run->error}</>");
        }

        $this->line('');
        $this->line('<fg=cyan>Trigger payload:</>');
        $this->line(json_encode($run->trigger_payload, JSON_PRETTY_PRINT));

        $this->line('');
        $this->line('<fg=cyan>Steps:</>');

        foreach ($run->runSteps()->orderBy('id')->get() as $step) {
            $color = match ($step->status) {
                'completed' => 'green',
                'failed' => 'red',
                default => 'gray',
            };

            $attemptsSuffix = $step->attempts > 1 ? ", {$step->attempts} attempts" : '';
            $this->line("  <fg={$color}>[{$step->status}]</> {$step->key}" . ($step->duration_ms !== null ? " ({$step->duration_ms}ms{$attemptsSuffix})" : ''));

            if ($step->output) {
                $this->line('    output: ' . json_encode($step->output));
            }

            if ($step->error) {
                $this->line("    <fg=red>error: {$step->error}</>");
            }
        }

        return self::SUCCESS;
    }
}
