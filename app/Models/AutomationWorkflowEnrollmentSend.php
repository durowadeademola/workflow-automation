<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per actual send attempt for one step of one journey enrollment —
 * gives per-step, per-channel analytics (open/click rate) without growing
 * an ever-larger json blob on the enrollment row itself.
 */
class AutomationWorkflowEnrollmentSend extends Model
{
    protected $fillable = [
        'enrollment_id',
        'step_key',
        'channel',
        'status',
        'note',
        'tracking_token',
        'sent_at',
        'opened_at',
        'clicked_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
            'opened_at' => 'datetime',
            'clicked_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(AutomationWorkflowEnrollment::class, 'enrollment_id');
    }
}
