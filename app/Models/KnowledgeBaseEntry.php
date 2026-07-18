<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBaseEntry extends Model
{
    public const TYPES = [
        'faq' => 'FAQ (Question & Answer)',
        'article' => 'Knowledge Base Article',
    ];

    protected $fillable = [
        'client_id',
        'type',
        'title',
        'content',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
