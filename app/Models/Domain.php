<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use SoftDeletes;

    public $table = 'domains';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'program_id',
        'url',
        'status',
        'is_subdomain',
        'parent_id',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function parent()
    {
        return $this->belongsTo(Domain::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Domain::class, 'parent_id');
    }
}
