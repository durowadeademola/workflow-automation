<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetMessage extends Model
{
    protected $fillable = [
        'widget_conversation_id',
        'sender_type',
        'sender_name',
        'content',
    ];

    public function conversation()
    {
        return $this->belongsTo(WidgetConversation::class, 'widget_conversation_id');
    }
}
