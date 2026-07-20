<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WidgetConversation extends Model
{
    protected $fillable = [
        'client_id',
        'agent_id',
        'session_token',
        'visitor_name',
        'status',
        'last_message_at',
        'waiting_since',
        'nudge_sent_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'waiting_since' => 'datetime',
        'nudge_sent_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function messages()
    {
        return $this->hasMany(WidgetMessage::class);
    }
}
