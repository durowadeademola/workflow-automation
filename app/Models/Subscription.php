<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use SoftDeletes;

    public $table = 'subscriptions';

    protected $fillable = [
        'client_id',
        'plan_id',
        'plan',
        'amount',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'status',
        'paystack_reference',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function planRecord()
    {
        return $this->belongsTo(Plan::class, 'plan_id');
    }

    public function isCurrentlyActive(): bool
    {
        return $this->status === 'active'
            && $this->is_active
            && $this->end_date
            && $this->end_date->isFuture();
    }
}
