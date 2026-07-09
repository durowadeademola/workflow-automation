<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'business_name',
        'email',
        'phone',
        'interest',
        'message',
        'source',
        'status',
    ];
}
