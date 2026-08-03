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
        'service',
        'billing_cycle',
        'amount',
        'credit_applied',
        'name',
        'start_date',
        'end_date',
        'is_active',
        'status',
        'cancelled_at',
        'cancellation_reason',
        'paystack_reference',
        'paystack_transaction_id',
        'paystack_amount_charged',
        'paystack_channel',
        'paystack_paid_at',
        'paystack_gateway_response',
        'limit_reached_notified_at',
        'expiry_reminder_sent_at',
        'rolled_over_appointments',
        'rolled_over_leads',
        'refund_status',
        'refund_amount',
        'refund_requested_at',
        'refund_reviewed_at',
        'refund_processed_at',
        'refund_reference',
        'refund_rejection_reason',
        'refund_original_end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'paystack_paid_at' => 'datetime',
        'limit_reached_notified_at' => 'datetime',
        'expiry_reminder_sent_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'refund_requested_at' => 'datetime',
        'refund_reviewed_at' => 'datetime',
        'refund_processed_at' => 'datetime',
        'refund_original_end_date' => 'date',
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

    /**
     * Human-readable service name — plan names alone are ambiguous now that
     * more than one service exists (both chat-widget and marketing-automation
     * have a "Professional" plan), so anywhere a subscription/invoice/email
     * names its plan should pair it with this. Null (pre-migration legacy
     * rows) was always chat-widget, back when it was the only service sold.
     */
    public function serviceLabel(): string
    {
        return match ($this->service ?? 'chat-widget') {
            'chat-widget' => 'Chat Widget',
            'marketing-automation' => 'Marketing Automation',
            default => $this->service,
        };
    }
}
