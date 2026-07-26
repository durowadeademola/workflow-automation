<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AutomationWorkflow extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'trigger_type',
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

    public function steps(): HasMany
    {
        return $this->hasMany(AutomationWorkflowStep::class)->orderBy('order');
    }

    public function runs(): HasMany
    {
        return $this->hasMany(AutomationWorkflowRun::class);
    }
}
