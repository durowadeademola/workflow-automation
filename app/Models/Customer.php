<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    public $table = 'customers';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'agent_id',
        'item_id',
        'name',
        'username',
        'chat_id',
        'state',
        'message',
        'platform',
        'product',
        'specs',
        'assigned_agent',
        'agent_email',
        'status'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function item()
    {
        return $this->belongsTo(Product::class);
    }
}
