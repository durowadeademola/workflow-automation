<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\WidgetConversation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AgentStats extends BaseWidget
{
    protected static bool $isLazy = false;

    // See AdminStats — Filament's default 5s auto-poll on stat widgets was
    // keeping this page in a constant background-refresh loop.
    protected ?string $pollingInterval = null;

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) $user && $user->is_agent;
    }

    protected function getStats(): array
    {
        $user = auth()->user();
        $clientId = $user->client_id;

        $waiting = WidgetConversation::where('client_id', $clientId)
            ->where('status', 'waiting')
            ->count();

        $active = WidgetConversation::where('client_id', $clientId)
            ->where('status', 'active')
            ->count();

        $resolvedToday = WidgetConversation::where('client_id', $clientId)
            ->where('status', 'closed')
            ->whereDate('updated_at', today())
            ->count();

        $myOrders = Order::where('client_id', $clientId)
            ->where('agent_id', $user->agent_id)
            ->count();

        return [
            Stat::make('Waiting for a Reply', $waiting)
                ->description($waiting > 0 ? 'Visitors asked for a human' : 'Nothing waiting')
                ->icon('heroicon-o-exclamation-circle')
                ->color($waiting > 0 ? 'danger' : 'success')
                ->url('/admin/live-chat'),

            Stat::make('Active Conversations', $active)
                ->description('Currently being handled')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->url('/admin/live-chat'),

            Stat::make('Resolved Today', $resolvedToday)
                ->description('Conversations closed today')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('My Orders', $myOrders)
                ->description('Assigned to you')
                ->icon('heroicon-o-shopping-cart')
                ->url('/admin/orders'),
        ];
    }
}
