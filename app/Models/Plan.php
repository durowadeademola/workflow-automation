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
        'promo_price',
        'promo_ends_at',
        'yearly_discount_percent',
        'message_limit',
        'appointment_limit',
        'lead_limit',
        'faq_limit',
        'contact_limit',
        'journey_limit',
        'email_send_limit',
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
        'promo_ends_at' => 'datetime',
        'yearly_discount_percent' => 'integer',
    ];

    /**
     * Appended so the public pricing page and client billing cards — both
     * of which only ever see plans through JSON (Inertia props or Livewire)
     * — get the computed promo/yearly state without needing to duplicate
     * this logic in JS.
     */
    protected $appends = [
        'has_active_promo', 'effective_price', 'promo_percent',
        'has_yearly_discount', 'yearly_regular_price', 'yearly_effective_price',
    ];

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * A promo is only "active" if a price is actually set, it's genuinely
     * cheaper than the regular price, and (if given an end date at all) it
     * hasn't passed yet — otherwise a stale, un-cleared promo would keep
     * showing forever.
     */
    public function getHasActivePromoAttribute(): bool
    {
        if (! $this->promo_price || $this->promo_price >= $this->amount) {
            return false;
        }

        return ! $this->promo_ends_at || $this->promo_ends_at->isFuture();
    }

    /**
     * What a client actually pays right now — the promo price while one's
     * active, the regular price otherwise. This is the only value
     * Billing::subscribe() should ever charge.
     */
    public function getEffectivePriceAttribute(): int
    {
        return $this->has_active_promo ? $this->promo_price : $this->amount;
    }

    public function getPromoPercentAttribute(): ?int
    {
        if (! $this->has_active_promo) {
            return null;
        }

        return (int) round((1 - $this->promo_price / $this->amount) * 100);
    }

    /**
     * A standing discount for choosing annual billing — unlike promo_price,
     * this never expires on its own (it's a billing-cycle choice, not a
     * time-boxed promotion), so it's only ever off when the admin hasn't
     * set a percentage at all.
     */
    public function getHasYearlyDiscountAttribute(): bool
    {
        return (int) $this->yearly_discount_percent > 0;
    }

    /**
     * The "old price" shown struck through for yearly billing — twelve
     * months at the plan's regular monthly rate. Deliberately based on the
     * regular amount, not any active monthly promo, so the two discounts
     * never stack into a confusing compounded price.
     */
    public function getYearlyRegularPriceAttribute(): int
    {
        return $this->amount * 12;
    }

    /**
     * What a client actually pays for a full year up front. This is the
     * only value Billing::subscribe() should ever charge for cycle=yearly.
     */
    public function getYearlyEffectivePriceAttribute(): int
    {
        if (! $this->has_yearly_discount) {
            return $this->yearly_regular_price;
        }

        return (int) round($this->yearly_regular_price * (1 - $this->yearly_discount_percent / 100));
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
