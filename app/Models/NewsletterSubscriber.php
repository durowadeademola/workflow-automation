<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Blueflow's own agency-level newsletter audience (public site sign-ups) —
 * see Newsletter/NewsletterSend for how this and Customer serve as the two
 * interchangeable recipient sources for a broadcast.
 */
class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'name',
        'email',
        'subscribed',
        'subscribed_at',
        'unsubscribed_at',
    ];

    protected function casts(): array
    {
        return [
            'subscribed' => 'boolean',
            'subscribed_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
