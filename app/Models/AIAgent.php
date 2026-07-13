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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
