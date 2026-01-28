<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agent extends Model
{
    use SoftDeletes;

    public $table = 'agents';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'name',
        'email',
        'telephone',
        'status'
    ];
}
