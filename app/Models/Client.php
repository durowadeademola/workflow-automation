<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    public $table = 'clients';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'email',
        'telephone',
        'type',
        'status',
        'webhook_url',
    ];

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Whether this client currently has a paid-up subscription. Checked
     * against end_date directly (not just the stored status) so an expired
     * period is caught immediately, without depending on a cron job having
     * already run to flip the status.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->startOfDay())
            ->exists();
    }
}
