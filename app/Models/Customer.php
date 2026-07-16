<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Customer extends Model
{
    use SoftDeletes;

    public $table = 'customers';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'client_id',
        'agent_id',
        'item_id',
        'name',
        'username',
        'chat_id',
        'state',
        'message',
        'platform',
        'product',
        'specs',
        'assigned_agent',
        'agent_email',
        'status',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function item()
    {
        return $this->belongsTo(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'customer_id');
    }

    /**
     * Anonymous visitors (the overwhelming majority of chat-widget chats)
     * never give a name, so `name` stays null — this gives them a stable,
     * human-scannable label instead of a blank cell. It's derived from
     * `chat_id` rather than stored, so it's consistent on every render and
     * automatically stops being used the moment a real name is captured.
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name ?: Str::upper(substr(md5((string) $this->chat_id), 0, 8));
    }

    /**
     * The shared identity lookup for every inbound channel (WhatsApp,
     * Telegram, the website widget, etc.) — a customer is the same person
     * across messages/orders as long as (client, platform, chat_id) match,
     * where `chat_id` is whatever that channel's own conversation id is
     * (a Telegram chat id, a WhatsApp number, the widget's session token).
     */
    public static function findOrCreateForChannel(
        int $clientId,
        string $platform,
        string $chatId,
        ?string $name = null,
    ): self {
        $customer = static::firstOrNew([
            'client_id' => $clientId,
            'platform' => $platform,
            'chat_id' => $chatId,
        ]);

        if (! $customer->exists) {
            $customer->status = 'OPEN';
        }

        if ($name && blank($customer->name)) {
            $customer->name = $name;
            $customer->username ??= $name;
        }

        if (! $customer->exists || $customer->isDirty()) {
            $customer->save();
        }

        return $customer;
    }
}
