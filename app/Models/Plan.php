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
     * Restricts to plans this client can actually see: universal plans
     * (service = null) plus any plan scoped to a service the client picked
     * at registration. A client with `features = null` (unrestricted/legacy)
     * sees everything, same rule as Client::hasFeature().
     */
    public function scopeForClient($query, ?Client $client)
    {
        $clientFeatures = $client?->features;

        if ($clientFeatures === null) {
            return $query;
        }

        return $query->where(function ($q) use ($clientFeatures) {
            $q->whereNull('service')->orWhereIn('service', $clientFeatures);
        });
    }
}
