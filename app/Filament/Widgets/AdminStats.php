<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Domain;
use App\Models\Vulnerability;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends BaseWidget
{
    // These are cheap counts against small, indexed tables — rendering them
    // with the initial page avoids a separate round-trip per card, which is
    // what was making the dashboard feel slow to "pop in" after login.
    protected static bool $isLazy = false;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()?->is_admin;
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Clients', Client::count())
                ->description('Total registered clients')
                ->icon('heroicon-o-building-office'),
            // ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            // ->color('success'),

            Stat::make('Domains', Domain::count())
                ->description('All domains and subdomains')
                ->icon('heroicon-o-globe-alt'),
            // ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            // ->color('primary'),

            Stat::make('Vulnerabilies', Vulnerability::count())
                ->description('Total found vulnerabilities')
                ->icon('heroicon-o-shield-exclamation'),
            // ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            // ->color('danger'),

            // Stat::make('Messages Today',
            //     Message::whereDate('created_at', today())->count()
            // )->icon('heroicon-o-chat-bubble-left-right'),
        ];
    }
}
