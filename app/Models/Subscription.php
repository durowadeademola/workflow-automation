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
        'credit_applied',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'status',
        'paystack_reference',
        'paystack_transaction_id',
        'paystack_amount_charged',
        'paystack_channel',
        'paystack_paid_at',
        'paystack_gateway_response',
        'limit_reached_notified_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'paystack_paid_at' => 'datetime',
        'limit_reached_notified_at' => 'datetime',
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
