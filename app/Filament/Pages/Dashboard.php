<?php

namespace App\Filament\Pages;

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
            ClientsChart::class,
            ClientStats::class,
            CustomersChart::class,
            OrdersChart::class,
            RecentMessages::class,
            RecentOrders::class,
        ];
    }
}
