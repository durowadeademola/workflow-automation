<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public $table = 'products';

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'price',
        'quantity',
        'currency',
        'is_available'
    ];
}
