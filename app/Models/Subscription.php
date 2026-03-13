<?php

namespace App\Models;

use App\Http\Traits\GuidId;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use GuidId, SoftDeletes;

    public $table = 'subscriptions';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
