<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public $table = 'customers';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'status'
    ];
}
