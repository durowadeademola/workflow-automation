<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AIAgent extends Model
{
    use SoftDeletes;

    public $table = 'ai_agents';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'customer_id',
        'client_id',
        'order_id',
        'product_id',
        'service_id',
        'source',
        'model',
        'prompt',
        'response',
        'success',
        'error',
        'latency',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'success' => 'boolean',
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
        return $this->hasMany(Product::class);
    }
}
