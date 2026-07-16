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
        'rejection_reason',
        'features',
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
        'features' => 'array',
    ];

    /**
     * The Blueflow automation services a client can pick at registration
     * (and admins can adjust afterward). Each slug that maps to dedicated
     * dashboard pages gates them — see WidgetSettings/LiveChat canAccess().
     */
    public const FEATURES = [
        'chat-widget' => 'Chat Widget',
        'whatsapp-automation' => 'WhatsApp Automation',
        'email-automation' => 'Email Automation',
        'payment-automation' => 'Payment Automation',
        'crm-integration' => 'CRM Integration',
        'workflow-automation' => 'Workflow Automation',
    ];

    /**
     * Only chat-widget is actually built right now — the rest of FEATURES
     * exists so the data model and admin tooling are ready for them, but
     * self-registration is deliberately restricted to this subset until
     * more services ship. Admins can still assign any FEATURES slug to a
     * client manually via ClientForm.
     */
    public const SELF_REGISTRATION_FEATURES = ['chat-widget'];

    /**
     * `null` means this client predates the feature-selection system (or an
     * admin hasn't set it) — treated as unrestricted rather than retroactively
     * locking out access nobody actually took away. Once a real array is set
     * (even an empty one), it's enforced as an actual allowlist.
     */
    public function hasFeature(string $slug): bool
    {
        if ($this->features === null) {
            return true;
        }

        return in_array($slug, $this->features, strict: true);
    }

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

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    public function kycSubmissions()
    {
        return $this->hasMany(KycSubmission::class);
    }

    /**
     * KYC is optional and never enforced anywhere — this is purely for the
     * admin/client to see status. A rejected submission can always be
     * resubmitted, which is why this is the latest row rather than a single
     * mutable one: the rejection history stays visible to the admin.
     */
    public function latestKyc(): ?KycSubmission
    {
        return $this->kycSubmissions()->orderByDesc('id')->first();
    }

    public function kycStatus(): string
    {
        return $this->latestKyc()?->status ?? 'not_submitted';
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

    public function currentSubscription(): ?Subscription
    {
        return $this->subscriptions()
            ->where('status', 'active')
            ->where('end_date', '>=', now()->startOfDay())
            ->latest('end_date')
            ->first();
    }

    /**
     * Free trials get a fixed cap since they aren't tied to a Plan record;
     * paid plans use whatever the admin set on that Plan (null = unlimited).
     */
    public const TRIAL_MESSAGE_LIMIT = 100;

    /**
     * Maps a plan's service to the Message `source` value(s) that count
     * toward its limit — so a WhatsApp/Telegram message never eats into a
     * chat-widget plan's cap (and vice versa) just because both happen to
     * share the same client. Only chat-widget has messaging-based billing
     * today; a service with no entry here falls back to counting every
     * source, same as a universal (service = null) plan.
     */
    public const SERVICE_MESSAGE_SOURCES = [
        'chat-widget' => ['Website'],
    ];

    public function messageLimitForCurrentPlan(): ?int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return 0;
        }

        if ($subscription->plan === 'trial') {
            return self::TRIAL_MESSAGE_LIMIT;
        }

        return $subscription->planRecord?->message_limit;
    }

    /**
     * Counted from the start of the current subscription period, not the
     * calendar month — plans run on their own 30-day cycle. Scoped to the
     * message source(s) that belong to the current plan's service, so
     * usage on a different channel doesn't count against this plan's cap.
     */
    public function messagesUsedInCurrentPeriod(): int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription || ! $subscription->start_date) {
            return 0;
        }

        $query = $this->messages()->where('created_at', '>=', $subscription->start_date);

        // The trial exists to try out the chat widget specifically — the
        // only feature that's actually usable today — so it's scoped the
        // same way a chat-widget plan would be.
        $service = $subscription->plan === 'trial' ? 'chat-widget' : $subscription->planRecord?->service;

        if ($sources = self::SERVICE_MESSAGE_SOURCES[$service] ?? null) {
            $query->whereIn('source', $sources);
        }

        return $query->count();
    }

    public function hasReachedMessageLimit(): bool
    {
        $limit = $this->messageLimitForCurrentPlan();

        if ($limit === null) {
            return false;
        }

        return $this->messagesUsedInCurrentPeriod() >= $limit;
    }

    /**
     * Same shape as the message limit above: trials get a fixed cap since
     * they aren't tied to a Plan record, paid plans use whatever the admin
     * set (null = unlimited).
     */
    public const TRIAL_APPOINTMENT_LIMIT = 5;

    public function appointmentLimitForCurrentPlan(): ?int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return 0;
        }

        if ($subscription->plan === 'trial') {
            return self::TRIAL_APPOINTMENT_LIMIT;
        }

        return $subscription->planRecord?->appointment_limit;
    }

    /**
     * Counted from the start of the current subscription period, not the
     * calendar month, same as messages. Cancelled appointments don't count
     * against the cap — cancelling one genuinely frees up capacity.
     */
    public function appointmentsBookedInCurrentPeriod(): int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription || ! $subscription->start_date) {
            return 0;
        }

        return $this->appointments()
            ->where('created_at', '>=', $subscription->start_date)
            ->where('status', '!=', 'cancelled')
            ->count();
    }

    public function hasReachedAppointmentLimit(): bool
    {
        $limit = $this->appointmentLimitForCurrentPlan();

        if ($limit === null) {
            return false;
        }

        return $this->appointmentsBookedInCurrentPeriod() >= $limit;
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
