<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    public $table = 'products';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'price',
        'quantity',
        'currency',
        'is_available',
        'image_path'
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
