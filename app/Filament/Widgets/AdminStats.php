<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Domain;
use App\Models\Lead;
use App\Models\Subscription;
use App\Models\Vulnerability;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends BaseWidget
{
    // These are cheap counts against small, indexed tables — rendering them
    // with the initial page avoids a separate round-trip per card, which is
    // what was making the dashboard feel slow to "pop in" after login.
    protected static bool $isLazy = false;

    // Filament's default 5s auto-poll on stats/chart widgets was keeping the
    // dashboard in a constant background-refresh loop, which could stall the
    // tab (and race with navigation, e.g. logging out) — plain counts don't
    // need near-real-time refresh, so this only updates on page load/reload.
    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()?->is_admin;
    }

    protected function getStats(): array
    {
        $pendingApprovals = Client::where('status', 'pending')->count();

        $activeSubscriptions = Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->count();

        $mrr = Subscription::where('status', 'active')
            ->where('end_date', '>=', now())
            ->sum('amount');

        $newLeads = Lead::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Pending Approvals', $pendingApprovals)
                ->description($pendingApprovals > 0 ? 'Businesses waiting to be let in' : 'All caught up')
                ->descriptionIcon('heroicon-m-clock')
                ->icon('heroicon-o-user-plus')
                ->color($pendingApprovals > 0 ? 'warning' : 'success')
                ->url('/admin/clients'),

            Stat::make('Active Clients', Client::where('status', 'active')->count())
                ->description('Approved and able to log in')
                ->icon('heroicon-o-building-office')
                ->url('/admin/clients'),

            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Currently paid up')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->url('/admin/subscriptions'),

            Stat::make('MRR', '₦'.number_format($mrr))
                ->description('From active subscriptions')
                ->icon('heroicon-o-banknotes')
                ->color('success'),

            Stat::make('New Leads', $newLeads)
                ->description('In the last 7 days')
                ->icon('heroicon-o-megaphone')
                ->url('/admin/leads'),

            Stat::make('Vulnerabilities', Vulnerability::count())
                ->description('Across '.Domain::count().' scanned domains')
                ->icon('heroicon-o-shield-exclamation')
                ->color('danger'),
        ];
    }
}
