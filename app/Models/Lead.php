<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Lead extends Model
{
    use Notifiable, SoftDeletes;

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
