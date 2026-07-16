<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Customer;
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

        $appointmentsBooked = Appointment::where('client_id', $clientId)->count();

        $leadsQualified = Customer::where('client_id', $clientId)
            ->where('is_qualified_lead', true)
            ->count();

        return [
            Stat::make('Waiting for a Reply', $waiting)
                ->description($waiting > 0 ? 'Visitors asked for a human' : 'Nothing waiting')
                ->icon('heroicon-o-exclamation-circle')
                ->color($waiting > 0 ? 'danger' : 'success')
                ->url('/user/live-chat'),

            Stat::make('Active Conversations', $active)
                ->description('Currently being handled')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary')
                ->url('/user/live-chat'),

            Stat::make('Resolved Today', $resolvedToday)
                ->description('Conversations closed today')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('Appointments Booked', $appointmentsBooked)
                ->description('Total appointments')
                ->icon('heroicon-o-calendar')
                ->url('/user/appointments'),

            Stat::make('Leads Qualified', $leadsQualified)
                ->description('Total qualified leads')
                ->icon('heroicon-o-user-plus')
                ->url('/user/customers'),
        ];
    }
}
