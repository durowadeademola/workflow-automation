<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use SoftDeletes;

    public $table = 'messages';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'customer_id',
        'content',
        'from_customer'
    ];
    
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
