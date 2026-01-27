<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    public $table = 'agents';

    protected $fillable = [
        'client_id',
        'name',
        'email',
        'status'
    ];
}
