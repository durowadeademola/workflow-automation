<?php

namespace App\Filament\Widgets;

use App\Models\Agent;
use App\Models\Client;
use App\Models\Customer;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStats extends BaseWidget
{
    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()->is_admin;
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

            Stat::make('Customers', Customer::count())
                ->description('All customers')
                ->icon('heroicon-o-users'),
            // ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            // ->color('primary'),

            Stat::make('Agents', Agent::count())
                ->description('All agents')
                ->icon('heroicon-o-user-group'),
            // ->descriptionIcon('heroicon-m-arrow-trending-up')
            // ->chart([7, 2, 10, 3, 15, 4, 17])
            // ->color('danger'),

            // Stat::make('Messages Today',
            //     Message::whereDate('created_at', today())->count()
            // )->icon('heroicon-o-chat-bubble-left-right'),
        ];
    }
}
