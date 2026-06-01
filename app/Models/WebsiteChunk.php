<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteChunk extends Model
{
    protected $fillable = [
        'client_id',
        'url',
        'content',
        'embedding',
        'metadata',
    ];

    protected $casts = [
        'embedding' => 'array',
        'metadata' => 'array',
    ];
}