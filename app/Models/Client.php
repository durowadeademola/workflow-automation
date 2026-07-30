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
        'bypass_plan_limits',
        'rejection_reason',
        'features',
        'webhook_url',
        'workflow_engine',
        'widget_ready',
        'widget_ready_at',
        'widget_enabled',
        'widget_agent_name',
        'widget_primary_color',
        'widget_greeting',
        'widget_wa_number',
        'widget_system_prompt',
        'widget_position',
        'widget_quick_replies',
        'widget_auto_open_delay',
        'working_hours_enabled',
        'working_days',
        'working_hours_start',
        'working_hours_end',
        'timezone',
    ];

    protected $casts = [
        'widget_quick_replies' => 'array',
        'features' => 'array',
        'widget_ready' => 'boolean',
        'widget_ready_at' => 'datetime',
        'widget_enabled' => 'boolean',
        'bypass_plan_limits' => 'boolean',
        'working_hours_enabled' => 'boolean',
        'working_days' => 'array',
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
     * Per-client, not a global switch — lets individual clients be moved
     * onto Blueflow's own AutomationWorkflow engine (app/Workflow/) one at a
     * time, independent of every other client, which stays on n8n until its
     * own workflow_engine is switched too. See WidgetChatController::send().
     */
    public function usesNativeWorkflowEngine(): bool
    {
        return $this->workflow_engine === 'native';
    }

    /**
     * Blocks a client from removing a service themselves while they still
     * have an active (or mid-checkout) subscription tied to a plan scoped
     * to that service — otherwise billing and access would disagree with
     * each other, with a paid subscription for a feature the client no
     * longer has. They have to cancel first.
     */
    public function hasActiveOrPendingSubscriptionForService(string $service): bool
    {
        return $this->subscriptions()
            ->whereIn('status', ['active', 'pending'])
            ->whereHas('planRecord', fn ($query) => $query->where('service', $service))
            ->exists();
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
        // Present in every client's widget unless they explicitly customize
        // it — gives visitors an explicit, discoverable set of next steps,
        // and gives the AI an unambiguous signal of intent when clicked.
        'widget_quick_replies' => ['Book an appointment', 'Chat with a staff member', 'Register with us'],
        'widget_auto_open_delay' => 1500,
        'working_hours_enabled' => false,
        'working_days' => ['mon', 'tue', 'wed', 'thu', 'fri'],
        'working_hours_start' => '09:00',
        'working_hours_end' => '17:00',
        'timezone' => 'Africa/Lagos',
    ];

    public const WORKING_DAYS = [
        'mon' => 'Monday',
        'tue' => 'Tuesday',
        'wed' => 'Wednesday',
        'thu' => 'Thursday',
        'fri' => 'Friday',
        'sat' => 'Saturday',
        'sun' => 'Sunday',
    ];

    /**
     * Whether the AI is currently allowed to hand this client's visitors
     * off to a human agent. `working_hours_enabled` is off by default, so
     * an unconfigured client behaves exactly as before — always available.
     */
    public function isWithinWorkingHours(): bool
    {
        if (! $this->working_hours_enabled) {
            return true;
        }

        $timezone = $this->timezone ?: 'Africa/Lagos';
        $now = now($timezone);

        $today = strtolower($now->format('D'));

        if (! in_array($today, $this->working_days ?? [], true)) {
            return false;
        }

        if (! $this->working_hours_start || ! $this->working_hours_end) {
            return true;
        }

        $start = $now->copy()->setTimeFromTimeString($this->working_hours_start);
        $end = $now->copy()->setTimeFromTimeString($this->working_hours_end);

        return $now->between($start, $end);
    }

    /**
     * A human-readable summary of this client's configured hours, for
     * feeding into the AI's own prompt — so it can correctly answer "are
     * you open right now?" itself, rather than the answer only ever
     * surfacing indirectly via WidgetChatController's post-handoff
     * override. Null when working_hours_enabled is off, matching
     * isWithinWorkingHours()'s "always available" default.
     */
    public function workingHoursDescription(): ?string
    {
        if (! $this->working_hours_enabled) {
            return null;
        }

        $days = collect($this->working_days ?? [])
            ->map(fn ($day) => self::WORKING_DAYS[$day] ?? null)
            ->filter()
            ->implode(', ');

        if ($days === '') {
            return null;
        }

        $timezone = $this->timezone ?: 'Africa/Lagos';

        if (! $this->working_hours_start || ! $this->working_hours_end) {
            return "{$days} ({$timezone})";
        }

        $start = \Illuminate\Support\Carbon::parse($this->working_hours_start)->format('g:i A');
        $end = \Illuminate\Support\Carbon::parse($this->working_hours_end)->format('g:i A');

        return "{$days}, {$start}–{$end} ({$timezone})";
    }

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

    public function knowledgeBaseEntries()
    {
        return $this->hasMany(KnowledgeBaseEntry::class);
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
        if ($this->bypass_plan_limits) {
            return true;
        }

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
     * Whatever subscription the client most recently had, active or not —
     * unlike currentSubscription(), this still finds one that's already
     * expired. Used for anything that needs to look back at "the period
     * that just ended" (appointment/lead rollover, message proration
     * credit) once the client is subscribing again after a gap.
     */
    public function mostRecentSubscription(): ?Subscription
    {
        return $this->subscriptions()->latest('created_at')->first();
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

        return $this->messageLimitForSubscription($subscription);
    }

    /**
     * Same as messageLimitForCurrentPlan(), but for any subscription —
     * including one that's already ended (see appointmentLimitForSubscription()).
     * Billing::calculateMessageProrationCredit() needs this exact scaled
     * value too — reading planRecord->message_limit directly there would
     * silently ignore the x12 yearly scaling and undercount unused capacity.
     */
    public function messageLimitForSubscription(Subscription $subscription): ?int
    {
        if ($subscription->plan === 'trial') {
            return self::TRIAL_MESSAGE_LIMIT;
        }

        return $this->scaleLimitForBillingCycle($subscription->planRecord?->message_limit, $subscription);
    }

    /**
     * A plan's message/appointment/lead limits are set per month — a yearly
     * subscription covers 12 of those months in one long period, so its cap
     * needs to scale accordingly rather than being stuck at one month's
     * worth of usage for the whole year. Unlimited (null) stays unlimited.
     */
    private function scaleLimitForBillingCycle(?int $limit, Subscription $subscription): ?int
    {
        if ($limit === null) {
            return null;
        }

        return $subscription->billing_cycle === 'yearly' ? $limit * 12 : $limit;
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

        return $this->messagesUsedInSubscriptionPeriod($subscription);
    }

    /**
     * Same source-scoping as messagesUsedInCurrentPeriod(), but usable for
     * any subscription — including one that has already ended — so a
     * just-expired period's usage can still be looked up (e.g. for
     * proration credit) after currentSubscription() stops returning it.
     */
    public function messagesUsedInSubscriptionPeriod(Subscription $subscription): int
    {
        if (! $subscription->start_date) {
            return 0;
        }

        // Excludes messages the widget logged but the AI never actually
        // answered (n8n unreachable, or returned nothing usable) — see
        // WidgetChatController, which is the only place this is ever set
        // to false. A failed attempt shouldn't eat into the plan limit.
        $query = $this->messages()
            ->where('counts_toward_limit', true)
            ->where('created_at', '>=', $subscription->start_date);

        if ($subscription->end_date) {
            $query->where('created_at', '<=', $subscription->end_date->copy()->endOfDay());
        }

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
        if ($this->bypass_plan_limits) {
            return false;
        }

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

        return $this->appointmentLimitForSubscription($subscription);
    }

    /**
     * Same as appointmentLimitForCurrentPlan(), but for any subscription —
     * including one that's already ended — so rollover calculations can
     * ask "what was this period's limit" without needing it to still be
     * the client's current one.
     */
    public function appointmentLimitForSubscription(Subscription $subscription): ?int
    {
        if ($subscription->plan === 'trial') {
            return self::TRIAL_APPOINTMENT_LIMIT;
        }

        $limit = $this->scaleLimitForBillingCycle($subscription->planRecord?->appointment_limit, $subscription);

        // Unlimited stays unlimited — rollover only matters when there's an
        // actual cap to extend. Only ever carried in from the immediately
        // preceding period (see Billing::subscribe()), and only when that
        // period's message cap — not just normal demand — is what left
        // this capacity unused, so plan tiers still mean something.
        return $limit === null ? null : $limit + $subscription->rolled_over_appointments;
    }

    /**
     * Counted from the start of the current subscription period, not the
     * calendar month, same as messages. Cancelled appointments don't count
     * against the cap — cancelling one genuinely frees up capacity.
     */
    public function appointmentsBookedInCurrentPeriod(): int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return 0;
        }

        return $this->appointmentsBookedInSubscriptionPeriod($subscription);
    }

    /**
     * Same as appointmentsBookedInCurrentPeriod(), but for any subscription
     * — including one that's already ended (see appointmentLimitForSubscription()).
     */
    public function appointmentsBookedInSubscriptionPeriod(Subscription $subscription): int
    {
        if (! $subscription->start_date) {
            return 0;
        }

        $query = $this->appointments()
            ->where('created_at', '>=', $subscription->start_date)
            ->where('status', '!=', 'cancelled');

        if ($subscription->end_date) {
            $query->where('created_at', '<=', $subscription->end_date->copy()->endOfDay());
        }

        return $query->count();
    }

    public function hasReachedAppointmentLimit(): bool
    {
        if ($this->bypass_plan_limits) {
            return false;
        }

        $limit = $this->appointmentLimitForCurrentPlan();

        if ($limit === null) {
            return false;
        }

        return $this->appointmentsBookedInCurrentPeriod() >= $limit;
    }

    /**
     * Same shape as the message/appointment limits above.
     */
    public const TRIAL_LEAD_LIMIT = 10;

    public function leadLimitForCurrentPlan(): ?int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return 0;
        }

        return $this->leadLimitForSubscription($subscription);
    }

    /**
     * Same as leadLimitForCurrentPlan(), but for any subscription —
     * including one that's already ended (see appointmentLimitForSubscription()).
     */
    public function leadLimitForSubscription(Subscription $subscription): ?int
    {
        if ($subscription->plan === 'trial') {
            return self::TRIAL_LEAD_LIMIT;
        }

        $limit = $this->scaleLimitForBillingCycle($subscription->planRecord?->lead_limit, $subscription);

        return $limit === null ? null : $limit + $subscription->rolled_over_leads;
    }

    /**
     * Counts distinct customers first qualified since the start of the
     * current subscription period, not the calendar month, same as messages
     * and appointments — re-qualifying an already-qualified customer (their
     * intent gets reiterated later in the conversation) never counts twice,
     * since `qualified_at` is only ever set once.
     */
    public function qualifiedLeadsInCurrentPeriod(): int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return 0;
        }

        return $this->qualifiedLeadsInSubscriptionPeriod($subscription);
    }

    /**
     * Same as qualifiedLeadsInCurrentPeriod(), but for any subscription —
     * including one that's already ended (see appointmentLimitForSubscription()).
     */
    public function qualifiedLeadsInSubscriptionPeriod(Subscription $subscription): int
    {
        if (! $subscription->start_date) {
            return 0;
        }

        $query = $this->customers()
            ->where('is_qualified_lead', true)
            ->where('qualified_at', '>=', $subscription->start_date);

        if ($subscription->end_date) {
            $query->where('qualified_at', '<=', $subscription->end_date->copy()->endOfDay());
        }

        return $query->count();
    }

    public function hasReachedLeadLimit(): bool
    {
        if ($this->bypass_plan_limits) {
            return false;
        }

        $limit = $this->leadLimitForCurrentPlan();

        if ($limit === null) {
            return false;
        }

        return $this->qualifiedLeadsInCurrentPeriod() >= $limit;
    }

    /**
     * Unlike message/appointment/lead limits, this isn't a per-billing-period
     * usage rate — it's a standing cap on how many FAQ entries can exist at
     * once, since FAQs answer instantly from the database with no AI/n8n
     * call involved (see WidgetFaqController) and so don't cost anything per
     * use the way a chat message does. No rollover concept applies for the
     * same reason.
     */
    public const TRIAL_FAQ_LIMIT = 5;

    public function faqLimitForCurrentPlan(): ?int
    {
        $subscription = $this->currentSubscription();

        if (! $subscription) {
            return 0;
        }

        if ($subscription->plan === 'trial') {
            return self::TRIAL_FAQ_LIMIT;
        }

        return $subscription->planRecord?->faq_limit;
    }

    public function faqsUsedCount(): int
    {
        return $this->knowledgeBaseEntries()->where('type', 'faq')->count();
    }

    public function hasReachedFaqLimit(): bool
    {
        if ($this->bypass_plan_limits) {
            return false;
        }

        $limit = $this->faqLimitForCurrentPlan();

        if ($limit === null) {
            return false;
        }

        return $this->faqsUsedCount() >= $limit;
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

        $config = array_merge(self::WIDGET_DEFAULTS, $stored, [
            'client_id' => $this->id,
            'business_name' => $this->name,
        ]);

        // The three baseline quick replies always show — appended after
        // whatever the client has added themselves, not replaced by it.
        // (array_merge above would otherwise let a client's own list wipe
        // these out entirely, since it's the same array key.)
        $config['widget_quick_replies'] = array_values(array_unique(array_merge(
            $stored['widget_quick_replies'] ?? [],
            self::WIDGET_DEFAULTS['widget_quick_replies'],
        )));

        return $config;
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

        // Query param changes whenever the file itself changes, so browsers
        // that already cached an older chat-widget.js fetch the new one
        // instead of silently keeping stale styles/behavior indefinitely.
        $widgetVersion = file_exists(public_path('chat-widget.js')) ? filemtime(public_path('chat-widget.js')) : time();
        $widgetSrc = asset('chat-widget.js').'?v='.$widgetVersion;

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
