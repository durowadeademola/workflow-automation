<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per actual recipient of a Newsletter — exists to give each send
 * its own unguessable tracking_token for the unsubscribe link. `subscriber`
 * resolves to either a Customer or a NewsletterSubscriber depending on
 * subscriber_type, since exactly two fixed audiences exist (not worth a real
 * Eloquent morphTo/polymorphic type map for just two).
 */
class NewsletterSend extends Model
{
    protected $fillable = [
        'newsletter_id',
        'subscriber_type',
        'subscriber_id',
        'tracking_token',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function newsletter(): BelongsTo
    {
        return $this->belongsTo(Newsletter::class);
    }

    public function subscriber(): Customer|NewsletterSubscriber|null
    {
        return match ($this->subscriber_type) {
            'customer' => Customer::find($this->subscriber_id),
            'subscriber' => NewsletterSubscriber::find($this->subscriber_id),
            default => null,
        };
    }
}
