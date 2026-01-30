<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStats extends BaseWidget
{
    protected static bool $isLazy = true;

    public static function canView(): bool
    {
        $user = auth()->user();

        /** * We check if the user exists, is a client, and if their
         * associated customer profile has the right type.
         */
        return $user
            && $user->is_client
            && $user->client?->type === 'online-store';
    }

    protected function getStats(): array
    {
        return [
            Stat::make('Customers', Customer::where(['client_id' => auth()->user()?->client_id])->count())
                ->description('Total customers')
                ->icon('heroicon-o-users'),
                //->descriptionIcon('heroicon-m-arrow-trending-up')
                //->chart([7, 2, 10, 3, 15, 4, 17])
                //->color('primary'),

            Stat::make('Products', Product::where(['client_id' => auth()->user()?->client_id,'is_available' => true])->count())
                ->description('Total available products')
                ->icon('heroicon-o-shopping-bag'),
                //->descriptionIcon('heroicon-m-arrow-trending-up')
                //->chart([7, 2, 10, 3, 15, 4, 17])
                //->color('success'),

            Stat::make('Orders', Order::where(['client_id' => auth()->user()?->client_id])->count())
                ->description('Total orders')
                ->icon('heroicon-o-shopping-cart'),
                //->descriptionIcon('heroicon-m-arrow-trending-up')
                //->chart([7, 2, 10, 3, 15, 4, 17])
                //->color('danger'),

            // Stat::make('Messages Today',
            //     Message::whereDate('created_at', today())->count()
            // )->icon('heroicon-o-chat-bubble-left-right'),
        ];
    }
}
