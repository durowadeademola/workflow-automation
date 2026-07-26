<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationWorkflowStep extends Model
{
    protected $fillable = [
        'automation_workflow_id',
        'key',
        'type',
        'config',
        'run_if',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'run_if' => 'array',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class, 'automation_workflow_id');
    }
}
