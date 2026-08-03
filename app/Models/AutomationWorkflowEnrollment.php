<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A single customer's stateful progress through one Marketing Journey
 * (AutomationWorkflow). Distinct from AutomationWorkflowRun, which is a
 * one-shot, synchronous, all-steps-now execution record for the
 * chat-widget-reply/crawler workflows — journeys need to persist across
 * days between steps, which that model doesn't support. Advanced by the
 * AdvanceMarketingJourneys command via JourneyStepAdvancer, not by
 * WorkflowExecutor.
 */
class AutomationWorkflowEnrollment extends Model
{
    protected $fillable = [
        'automation_workflow_id',
        'client_id',
        'customer_id',
        'status',
        'current_step_order',
        'next_run_at',
        'context',
        'enrolled_at',
        'completed_at',
        'exited_at',
        'exit_reason',
    ];

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'next_run_at' => 'datetime',
            'enrolled_at' => 'datetime',
            'completed_at' => 'datetime',
            'exited_at' => 'datetime',
        ];
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class, 'automation_workflow_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(AutomationWorkflowEnrollmentSend::class, 'enrollment_id');
    }
}
