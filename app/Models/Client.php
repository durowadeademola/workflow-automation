<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    public $table = 'clients';

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'email',
        'telephone',
        'type',
        'status',
        'webhook_url',
        'widget_agent_name',
        'widget_primary_color',
        'widget_greeting',
        'widget_wa_number',
        'widget_system_prompt',
        'widget_position',
        'widget_quick_replies',
        'widget_auto_open_delay',
    ];

    protected $casts = [
        'widget_quick_replies' => 'array',
    ];

    /**
     * Fallbacks used until a client customizes their widget — kept in sync
     * with the defaults baked into public/chat-widget.js itself.
     */
    public const WIDGET_DEFAULTS = [
        'widget_agent_name' => 'AI Assistant',
        'widget_primary_color' => '#2563EB',
        'widget_greeting' => '👋 Hello! How can I help you today?',
        'widget_wa_number' => '',
        'widget_system_prompt' => 'You are a helpful AI assistant for this business. Be concise, friendly, and helpful.',
        'widget_position' => 'right',
        'widget_quick_replies' => [],
        'widget_auto_open_delay' => 1500,
    ];

    public function agents()
    {
        return $this->hasMany(Agent::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Whether this client currently has a paid-up subscription. Checked
     * against end_date directly (not just the stored status) so an expired
     * period is caught immediately, without depending on a cron job having
     * already run to flip the status.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->startOfDay())
            ->exists();
    }

    /**
     * The widget's live config, with stored per-client customization layered
     * over the shared defaults. Used to render both the copyable embed
     * snippet and the admin's read-only preview, so the two can never drift.
     */
    public function getWidgetConfig(): array
    {
        $stored = array_filter(
            $this->only(array_keys(self::WIDGET_DEFAULTS)),
            fn ($value) => $value !== null && $value !== [],
        );

        return array_merge(self::WIDGET_DEFAULTS, $stored, [
            'client_id' => $this->id,
            'business_name' => $this->name,
        ]);
    }

    /**
     * A JSON-encode for embedding in a literal <script> block that a client
     * copies onto their own site — kept human-readable (plain slashes, plain
     * unicode/emoji, no '-style escapes) since this is meant to be read,
     * not just executed. The only real risk when inlining JSON inside a
     * <script> tag is a value containing a literal "</script" sequence
     * closing the tag early, so that's neutralized explicitly rather than
     * escaping everything else along with it.
     */
    private static function jsValue(mixed $value): string
    {
        $json = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return str_ireplace('</script', '<\/script', $json);
    }

    /**
     * The literal <script> block a client pastes onto their own site.
     */
    public function getWidgetEmbedSnippet(): string
    {
        $config = $this->getWidgetConfig();

        $lines = [
            'clientId' => self::jsValue($config['client_id']),
            'apiBase' => self::jsValue(url('/api')),
            'businessName' => self::jsValue($config['business_name']),
            'agentName' => self::jsValue($config['widget_agent_name']),
            'primaryColor' => self::jsValue($config['widget_primary_color']),
            'waNumber' => self::jsValue($config['widget_wa_number']),
            'greeting' => self::jsValue($config['widget_greeting']),
            'systemPrompt' => self::jsValue($config['widget_system_prompt']),
            'position' => self::jsValue($config['widget_position']),
            'quickReplies' => self::jsValue($config['widget_quick_replies']),
            'autoOpenDelay' => self::jsValue($config['widget_auto_open_delay']),
        ];

        $body = collect($lines)
            ->map(fn ($value, $key) => "        {$key}: {$value},")
            ->implode("\n");

        $widgetSrc = asset('chat-widget.js');

        return <<<HTML
        <script>
            window.ChatWidgetConfig = {
        {$body}
            };
        </script>
        <script src="{$widgetSrc}"></script>
        HTML;
    }
}
