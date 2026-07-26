<?php

namespace App\Console\Commands;

use App\Models\AutomationWorkflow;
use App\Workflow\WorkflowExecutor;
use Cron\CronExpression;
use Illuminate\Console\Command;

class RunScheduledWorkflows extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'workflows:run-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs every active AutomationWorkflow with trigger_type=scheduled whose cron expression is due right now.';

    public function handle(WorkflowExecutor $executor): int
    {
        $workflows = AutomationWorkflow::where('trigger_type', 'scheduled')
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            $cron = $workflow->trigger_config['cron'] ?? null;

            if (! $cron) {
                $this->warn("Workflow [{$workflow->slug}] has trigger_type=scheduled but no \"cron\" key in trigger_config — skipping.");

                continue;
            }

            if (! (new CronExpression($cron))->isDue()) {
                continue;
            }

            $run = $executor->run($workflow, ['triggeredAt' => now()->toIso8601String()]);

            $workflow->update(['last_triggered_at' => now()]);

            $this->info("Ran workflow [{$workflow->slug}] — run #{$run->id} ({$run->status}).");
        }

        return self::SUCCESS;
    }
}
