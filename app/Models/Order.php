<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    public $table = 'orders';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'customer_id',
        'agent_id',
        'product_id',
        'service_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'order_reference',
        'amount',
        'currency',
        'status',
        'source',
        'notes'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
