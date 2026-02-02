<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Workflow extends Model
{
    use SoftDeletes;

    public $table = 'workflows';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'platform',
        'is_published',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
