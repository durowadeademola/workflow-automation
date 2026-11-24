<?php

namespace App\Filament\Pages;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Subscription;
use App\Models\User;
use App\Models\WidgetConversation;
use App\Models\WidgetMessage;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Custom dashboard (resources/views/filament/pages/custom-dashboard.blade.php): Filament's own page
 * header carries the greeting, the one-line finding and the shortcut buttons, followed by a joined
 * KPI strip and a two-column grid of plain panels — rather than Filament's default stack of
 * stat/chart widgets (still kept in app/Filament/Widgets, just unused, in case they're wanted back).
 *
 * A user here only ever holds one of is_admin/is_client/is_agent, so exactly one role's section
 * renders.
 */
class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::Home;

    protected string $view = 'filament.pages.custom-dashboard';

    public function getHeading(): string
    {
        $hour = now()->hour;
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

        return $greeting.', '.($this->dashboard['firstName'] ?? 'there');
    }

    public function getSubheading(): ?string
    {
        return $this->dashboard['finding'] ?? 'Here\'s what\'s happening today.';
    }

    // Header actions are cached after mount(), so $this->dashboard is already filled in here.
    protected function getHeaderActions(): array
    {
        return collect($this->dashboard['actions'] ?? [])
            ->map(fn (array $link, int $index): Action => Action::make("dashboardLink{$index}")
                ->label($link['label'])
                ->url($link['url'])
                ->color($link['primary'] ? 'primary' : 'gray'))
            ->all();
    }

    // Livewire only auto-exposes PUBLIC PROPERTIES to the rendered view (not a getViewData()-style
    // method — that's a plain Blade convention, not something Livewire looks for), so the role data
    // is computed once here, in the standard mount() lifecycle hook, into this one property.
    public array $dashboard = [];

    public function mount(): void
    {
        /** @var User|null $user */
        $user = Auth::user();
        $firstName = filled($user?->name) ? Str::before($user->name, ' ') : 'there';

        if ($user?->is_admin) {
            $this->dashboard = ['role' => 'admin', 'firstName' => $firstName] + $this->adminData();
        } elseif ($user?->is_client) {
            $this->dashboard = ['role' => 'client', 'firstName' => $firstName] + $this->clientData($user);
        } elseif ($user?->is_agent) {
            $this->dashboard = ['role' => 'agent', 'firstName' => $firstName] + $this->agentData($user);
        } else {
            $this->dashboard = ['role' => null, 'firstName' => $firstName];
        }
    }

    /** @return array<string, mixed> */
    private function adminData(): array
    {
        $pendingApprovals = Client::where('status', 'pending')->count();
        $activeClients = Client::where('status', 'active')->count();

        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->count();

        $mrr = (float) Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->sum('amount');

        $newLeads = Lead::where('created_at', '>=', now()->subDays(7))->count();

        $expiringSoon = Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->count();

        $finding = ($pendingApprovals > 0 || $expiringSoon > 0)
            ? "{$pendingApprovals} ".Str::plural('business', $pendingApprovals).' waiting to be approved, '
                ."{$expiringSoon} ".Str::plural('subscription', $expiringSoon).' expiring within 7 days.'
            : 'Nothing needs your attention right now.';

        $expiringSubscriptions = Subscription::with('client')
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->where('end_date', '<=', now()->addDays(7))
            ->orderBy('end_date')
            ->limit(8)
            ->get();

        $recentClients = Client::orderByDesc('created_at')->limit(8)->get();
        $recentLeads = Lead::orderByDesc('created_at')->limit(5)->get();

        return [
            'finding' => $finding,
            'mrr' => $mrr,
            'kpis' => [
                ['label' => 'Pending approvals', 'value' => $pendingApprovals, 'desc' => $pendingApprovals > 0 ? 'Businesses waiting to be let in' : 'All caught up', 'tone' => $pendingApprovals > 0 ? 'warning' : null],
                ['label' => 'Active clients', 'value' => $activeClients, 'desc' => 'Approved and able to log in'],
                ['label' => 'Active subscriptions', 'value' => $activeSubscriptions, 'desc' => 'Currently paid up'],
                ['label' => 'MRR', 'value' => '₦'.number_format($mrr), 'desc' => 'From active subscriptions'],
                ['label' => 'New leads', 'value' => $newLeads, 'desc' => 'In the last 7 days'],
                ['label' => 'Expiring soon', 'value' => $expiringSoon, 'desc' => $expiringSoon > 0 ? 'Within 7 days' : 'Nothing ending soon', 'tone' => $expiringSoon > 0 ? 'warning' : null],
            ],
            'subscriptionSplit' => ['active' => $activeSubscriptions, 'expiringSoon' => $expiringSoon],
            'expiringSubscriptions' => $expiringSubscriptions,
            'recentClients' => $recentClients,
            'recentLeads' => $recentLeads,
            'actions' => [
                ['label' => 'Review clients', 'url' => '/admin/clients', 'primary' => $pendingApprovals > 0],
                ['label' => 'Subscriptions', 'url' => '/admin/subscriptions', 'primary' => false],
                ['label' => 'Leads', 'url' => '/admin/leads', 'primary' => false],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function clientData(User $user): array
    {
        $clientId = $user->client_id;
        $client = Client::find($clientId);

        $messagesUsed = $client?->messagesUsedInCurrentPeriod() ?? 0;
        $messageLimit = $client?->messageLimitForCurrentPlan();
        $messagePercent = $messageLimit ? min(100, (int) round($messagesUsed / $messageLimit * 100)) : 0;

        $messagesToday = Message::where('client_id', $clientId)->whereDate('created_at', today())->count();
        $totalCustomers = Customer::where('client_id', $clientId)->count();
        $qualifiedLeads = Customer::where('client_id', $clientId)->where('is_qualified_lead', true)->count();
        $appointmentsBooked = Appointment::where('client_id', $clientId)->count();

        $finding = "{$messagesToday} ".Str::plural('message', $messagesToday).' today across all channels.'
            .($messageLimit ? " {$messagePercent}% of this period's plan limit used." : '');

        $recentMessages = Message::with('customer')
            ->where('client_id', $clientId)
            ->select('messages.*')
            ->whereIn('id', function ($query) use ($clientId) {
                $query->selectRaw('MAX(id)')->from('messages')->where('client_id', $clientId)->groupBy('customer_id');
            })
            ->latest()
            ->limit(8)
            ->get();

        $upcomingAppointments = Appointment::where('client_id', $clientId)
            ->where('status', '!=', 'cancelled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(8)
            ->get();

        return [
            'finding' => $finding,
            'subscriptionSummary' => $this->clientSubscriptionSummary($client),
            'kpis' => [
                ['label' => 'Customers', 'value' => $totalCustomers, 'desc' => 'Total customers'],
                ['label' => 'Appointments booked', 'value' => $appointmentsBooked, 'desc' => 'Total appointments'],
                ['label' => 'Leads qualified', 'value' => $qualifiedLeads, 'desc' => 'Total qualified leads'],
                ['label' => 'Messages today', 'value' => $messagesToday, 'desc' => 'Across all channels'],
                ['label' => 'Messages this period', 'value' => $messageLimit ? "{$messagesUsed} / {$messageLimit}" : (string) $messagesUsed, 'desc' => $messageLimit ? "{$messagePercent}% of plan limit used" : 'Unlimited on your plan'],
            ],
            'qualifiedRate' => $totalCustomers > 0 ? (int) round($qualifiedLeads / $totalCustomers * 100) : 0,
            'totalCustomers' => $totalCustomers,
            'qualifiedLeads' => $qualifiedLeads,
            'recentMessages' => $recentMessages,
            'upcomingAppointments' => $upcomingAppointments,
            'actions' => [
                ['label' => 'Customers', 'url' => '/user/customers', 'primary' => true],
                ['label' => 'Appointments', 'url' => '/user/appointments', 'primary' => false],
                ['label' => 'Billing', 'url' => '/user/billing', 'primary' => false],
            ],
        ];
    }

    /** @return array<string, mixed> */
    private function agentData(User $user): array
    {
        $clientId = $user->client_id;

        $waiting = WidgetConversation::where('client_id', $clientId)->where('status', 'waiting')->count();
        $active = WidgetConversation::where('client_id', $clientId)->where('status', 'active')->count();
        $resolvedToday = WidgetConversation::where('client_id', $clientId)
            ->where('status', 'closed')
            ->whereDate('updated_at', today())
            ->count();

        $appointmentsBooked = Appointment::where('client_id', $clientId)->count();
        $totalCustomers = Customer::where('client_id', $clientId)->count();
        $qualifiedLeads = Customer::where('client_id', $clientId)->where('is_qualified_lead', true)->count();

        // sender_name is only ever set for a genuine agent reply (see LiveChat::sendReply());
        // auto-inserted system messages leave it null, so this doesn't inflate the count.
        $messagesHandledToday = WidgetMessage::whereHas('conversation', fn ($query) => $query->where('client_id', $clientId))
            ->where('sender_type', 'agent')
            ->whereNotNull('sender_name')
            ->whereDate('created_at', today())
            ->count();

        $finding = $waiting > 0
            ? "{$waiting} ".Str::plural('visitor', $waiting).' waiting for a reply.'
            : 'Nothing waiting for you right now.';

        $waitingConversations = WidgetConversation::where('client_id', $clientId)
            ->where('status', 'waiting')
            ->orderBy('waiting_since')
            ->limit(8)
            ->get();

        $upcomingAppointments = Appointment::where('client_id', $clientId)
            ->where('status', '!=', 'cancelled')
            ->where('scheduled_at', '>=', now())
            ->orderBy('scheduled_at')
            ->limit(8)
            ->get();

        return [
            'finding' => $finding,
            'resolvedToday' => $resolvedToday,
            'messagesHandledToday' => $messagesHandledToday,
            'kpis' => [
                ['label' => 'Waiting for a reply', 'value' => $waiting, 'desc' => $waiting > 0 ? 'Visitors asked for a human' : 'Nothing waiting', 'tone' => $waiting > 0 ? 'warning' : null],
                ['label' => 'Active conversations', 'value' => $active, 'desc' => 'Currently being handled'],
                ['label' => 'Resolved today', 'value' => $resolvedToday, 'desc' => 'Conversations closed today'],
                ['label' => 'Appointments booked', 'value' => $appointmentsBooked, 'desc' => 'Total appointments'],
                ['label' => 'Leads qualified', 'value' => $qualifiedLeads, 'desc' => 'Total qualified leads'],
                ['label' => 'Messages handled today', 'value' => $messagesHandledToday, 'desc' => 'Agent replies sent today'],
            ],
            'conversationSplit' => ['waiting' => $waiting, 'active' => $active, 'resolvedToday' => $resolvedToday],
            'waitingConversations' => $waitingConversations,
            'upcomingAppointments' => $upcomingAppointments,
            'qualifiedRate' => $totalCustomers > 0 ? (int) round($qualifiedLeads / $totalCustomers * 100) : 0,
            'totalCustomers' => $totalCustomers,
            'qualifiedLeads' => $qualifiedLeads,
            'actions' => [
                ['label' => 'Live chat', 'url' => '/user/live-chat', 'primary' => $waiting > 0],
                ['label' => 'Appointments', 'url' => '/user/appointments', 'primary' => false],
                ['label' => 'Customers', 'url' => '/user/customers', 'primary' => false],
            ],
        ];
    }

    /**
     * Mirrors ClientStats::getSubscriptionStats() — one line per service the client currently
     * has (or last had) an active subscription for.
     *
     * @return array{status: 'none'|'active'|'expiring', lines: array<int, string>}
     */
    private function clientSubscriptionSummary(?Client $client): array
    {
        $none = ['status' => 'none', 'lines' => ['No active plan']];

        if (! $client) {
            return $none;
        }

        $services = $client->features === null
            ? Client::SELF_REGISTRATION_FEATURES
            : array_values(array_intersect(Client::SELF_REGISTRATION_FEATURES, $client->features));

        $lines = [];
        $anyExpiring = false;

        foreach ($services as $service) {
            $subscription = $client->currentSubscription($service);

            if (! $subscription) {
                continue;
            }

            $isExpiring = $subscription->end_date && $subscription->end_date->isPast();
            $anyExpiring = $anyExpiring || $isExpiring;

            $lines[] = $subscription->serviceLabel().' ('.$subscription->name.'): '.($isExpiring
                ? 'expired '.$subscription->end_date->diffForHumans()
                : 'expires '.$subscription->end_date->diffForHumans());
        }

        if (empty($lines)) {
            return $none;
        }

        return ['status' => $anyExpiring ? 'expiring' : 'active', 'lines' => $lines];
    }
}
