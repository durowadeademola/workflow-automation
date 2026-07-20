<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AdminStats;
use App\Filament\Widgets\AgentStats;
use App\Filament\Widgets\ClientsChart;
use App\Filament\Widgets\ClientStats;
use App\Filament\Widgets\CustomersChart;
use App\Filament\Widgets\OrdersChart;
use App\Filament\Widgets\RecentAppointments;
use App\Filament\Widgets\RecentMessages;
use App\Filament\Widgets\SubscriptionsChart;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

class Dashboard extends BaseDashboard
{
    protected static BackedEnum|string|null $navigationIcon = Heroicon::Home;

    public function getWidgets(): array
    {
        return [
            AdminStats::class,
            ClientStats::class,
            AgentStats::class,
            ClientsChart::class,
            SubscriptionsChart::class,
            CustomersChart::class,
            OrdersChart::class,
            RecentMessages::class,
            RecentAppointments::class,
        ];
    }
}
