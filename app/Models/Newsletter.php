<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A one-off broadcast email. `client_id` null is Blueflow's own agency
 * newsletter (sent to NewsletterSubscriber rows); set is a client's own
 * broadcast to their Customers — same nullable-client_id convention
 * AutomationWorkflow already uses to distinguish shared/system rows from a
 * client's own.
 */
class Newsletter extends Model
{
    protected $fillable = [
        'client_id',
        'subject',
        'body_html',
        'status',
        'sent_at',
        'recipients_count',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function sends(): HasMany
    {
        return $this->hasMany(NewsletterSend::class);
    }
}
