<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\Customer;
use App\Models\Message;
use App\Models\Order;
use App\Models\Product;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientStats extends BaseWidget
{
    protected static bool $isLazy = false;

    // See AdminStats — Filament's default 5s auto-poll on stat widgets was
    // keeping this page in a constant background-refresh loop.
    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) $user && $user->is_client;
    }

    protected function getStats(): array
    {
        $clientId = auth()->user()?->client_id;
        $client = Client::find($clientId);

        $subscription = Subscription::where('client_id', $clientId)
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->latest('end_date')
            ->first();

        $messagesUsed = $client?->messagesUsedInCurrentPeriod() ?? 0;
        $messageLimit = $client?->messageLimitForCurrentPlan();
        $messagePercent = $messageLimit ? min(100, (int) round($messagesUsed / $messageLimit * 100)) : 0;

        return [
            Stat::make(
                'Subscription',
                $subscription ? $subscription->name : 'No active plan',
            )
                ->description(
                    $subscription
                        ? 'Renews '.$subscription->end_date->diffForHumans()
                        : 'Subscribe to keep your widget running'
                )
                ->icon('heroicon-o-credit-card')
                ->color($subscription ? 'success' : 'danger')
                ->url('/user/billing'),

            Stat::make('Customers', Customer::where('client_id', $clientId)->count())
                ->description('Total customers')
                ->icon('heroicon-o-users'),

            Stat::make('Orders', Order::where('client_id', $clientId)->count())
                ->description('Total orders')
                ->icon('heroicon-o-shopping-cart')
                ->url('/user/orders'),

            Stat::make('Products', Product::where('client_id', $clientId)->where('is_available', true)->count())
                ->description('Total available products')
                ->icon('heroicon-o-shopping-bag'),

            Stat::make('Messages Today', Message::where('client_id', $clientId)->whereDate('created_at', today())->count())
                ->description('Across all channels')
                ->icon('heroicon-o-chat-bubble-left-right'),

            Stat::make(
                'Messages This Period',
                $messageLimit ? "{$messagesUsed} / {$messageLimit}" : (string) $messagesUsed,
            )
                ->description($messageLimit ? "{$messagePercent}% of your plan's limit used" : 'Unlimited on your plan')
                ->icon('heroicon-o-chart-bar')
                ->color($messageLimit && $messagePercent >= 100 ? 'danger' : ($messageLimit && $messagePercent >= 80 ? 'warning' : 'success'))
                ->url('/user/billing'),
        ];
    }
}
