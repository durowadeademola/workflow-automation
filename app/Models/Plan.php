<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'service',
        'amount',
        'message_limit',
        'description',
        'features',
        'is_popular',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'is_popular' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    /**
     * Restricts to plans this client can actually see. A client with
     * `features = null` (unrestricted/legacy) sees everything, same rule as
     * Client::hasFeature(). Otherwise it's a strict match: only plans scoped
     * to one of the client's selected services — universal plans
     * (service = null) are NOT shown as a fallback, so every service a
     * client can pick needs its own plans or that client sees none at all.
     */
    public function scopeForClient($query, ?Client $client)
    {
        $clientFeatures = $client?->features;

        if ($clientFeatures === null) {
            return $query;
        }

        return $query->whereIn('service', $clientFeatures);
    }
}
