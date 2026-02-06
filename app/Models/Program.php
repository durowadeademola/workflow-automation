<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use SoftDeletes;

    public $table = 'programs';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'name', 
        'description',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
