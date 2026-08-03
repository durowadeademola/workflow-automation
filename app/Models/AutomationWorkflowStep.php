<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationWorkflowStep extends Model
{
    /**
     * The small, fixed set of exit conditions a Marketing Journey step can
     * offer through a plain dropdown (see MarketingJourneyForm), each
     * mapping to the {field, equals?} shape JourneyStepAdvancer's
     * conditionMet() understands. '' (never) stores exit_if = null.
     */
    public const EXIT_CONDITION_PRESETS = [
        '' => null,
        'appointment_booked' => ['field' => 'trigger.latestAppointment'],
        'unsubscribed' => ['field' => 'trigger.customer.subscribedToMarketing', 'equals' => false],
    ];

    protected $fillable = [
        'automation_workflow_id',
        'key',
        'type',
        'config',
        'run_if',
        'exit_if',
        'exit_condition_preset',
        'canvas_position',
        'max_attempts',
        'retry_delay_ms',
        'wait_amount',
        'wait_unit',
        'channel',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'config' => 'array',
            'run_if' => 'array',
            'exit_if' => 'array',
            'canvas_position' => 'array',
        ];
    }

    /**
     * A virtual attribute, not a real column — lets the Filament form bind
     * a single friendly Select directly to the underlying exit_if json,
     * without the form needing to know its {field, equals} shape at all.
     */
    protected function exitConditionPreset(): Attribute
    {
        return Attribute::make(
            get: function () {
                foreach (self::EXIT_CONDITION_PRESETS as $key => $condition) {
                    if ($key !== '' && $this->exit_if == $condition) {
                        return $key;
                    }
                }

                return '';
            },
            set: fn ($value) => ['exit_if' => self::EXIT_CONDITION_PRESETS[$value] ?? null],
        );
    }

    public function workflow(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflow::class, 'automation_workflow_id');
    }
}
