<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    public $table = 'messages';

    protected $fillable = [
        'client_id',
        'customer_id',
        'content',
        'from_customer'
    ];
}
