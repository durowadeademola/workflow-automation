<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Customer;
use App\Models\Message;
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

            Stat::make('Appointments Booked', Appointment::where('client_id', $clientId)->count())
                ->description('Total appointments')
                ->icon('heroicon-o-calendar')
                ->url('/user/appointments'),

            Stat::make('Leads Qualified', Customer::where('client_id', $clientId)->where('is_qualified_lead', true)->count())
                ->description('Total qualified leads')
                ->icon('heroicon-o-user-plus')
                ->url('/user/customers'),

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
