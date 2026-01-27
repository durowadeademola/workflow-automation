<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    public $table = 'customers';

    protected $fillable = [
        'client_id',
        'agent_id',
        'product_id',
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
}
