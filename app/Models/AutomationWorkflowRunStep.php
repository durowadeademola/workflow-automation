<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationWorkflowRunStep extends Model
{
    protected $fillable = [
        'automation_workflow_run_id',
        'automation_workflow_step_id',
        'key',
        'status',
        'input',
        'output',
        'error',
        'duration_ms',
    ];

    protected function casts(): array
    {
        return [
            'input' => 'array',
            'output' => 'array',
        ];
    }

    public function run(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflowRun::class, 'automation_workflow_run_id');
    }

    public function step(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflowStep::class, 'automation_workflow_step_id');
    }
}
