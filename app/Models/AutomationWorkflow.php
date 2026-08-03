<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationWorkflow extends Model
{
    protected $fillable = [
        'client_id',
        'name',
        'slug',
        'trigger_type',
        'trigger_event',
        'trigger_config',
        'description',
        'is_active',
        'last_triggered_at',
    ];

    protected function casts(): array
    {
        return [
            'trigger_config' => 'array',
            'is_active' => 'boolean',
            'last_triggered_at' => 'datetime',
        ];
    }

    /**
     * Null = a shared, client-agnostic system workflow (chat-widget-reply,
     * website-crawler) — every client triggers the same row through their
     * own payload. Set = a specific client's own workflow; Marketing
     * Journeys are the first feature to actually populate this.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function steps(): HasMany
    {
        return $this->hasMany(AutomationWorkflowStep::class)->orderBy('order');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AutomationWorkflowRun::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(AutomationWorkflowEnrollment::class);
    }
}
