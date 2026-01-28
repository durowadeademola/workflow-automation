<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use SoftDeletes;

    public $table = 'services';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'price',
        'currency',
        'is_active'
    ];
}
