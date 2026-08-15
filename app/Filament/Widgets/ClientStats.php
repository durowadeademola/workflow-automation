<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Client;
use App\Models\Customer;
use App\Models\Message;
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

        $messagesUsed = $client?->messagesUsedInCurrentPeriod() ?? 0;
        $messageLimit = $client?->messageLimitForCurrentPlan();
        $messagePercent = $messageLimit ? min(100, (int) round($messagesUsed / $messageLimit * 100)) : 0;

        return [
            ...$this->getSubscriptionStats($client),

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

    /**
     * A single combined stat covering every billable service the client
     * currently has an active subscription for — previously this was a
     * single query with no `service` filter that picked whichever
     * subscription had the latest `end_date` across every service, so a
     * client with both chat-widget (ending today) and marketing-automation
     * (ending later) only ever saw marketing-automation here, silently
     * hiding the more urgent one.
     */
    private function getSubscriptionStats(?Client $client): array
    {
        $noActivePlan = [
            Stat::make('Subscription', 'No active plan')
                ->description('Subscribe to keep your widget running')
                ->icon('heroicon-o-credit-card')
                ->color('danger')
                ->url('/user/billing'),
        ];

        if (! $client) {
            return $noActivePlan;
        }

        $services = $client->features === null
            ? Client::SELF_REGISTRATION_FEATURES
            : array_values(array_intersect(Client::SELF_REGISTRATION_FEATURES, $client->features));

        $lines = [];
        $anyExpiring = false;

        foreach ($services as $service) {
            $subscription = $client->currentSubscription($service);

            if (! $subscription) {
                continue;
            }

            $isExpiring = $subscription->end_date && $subscription->end_date->isPast();
            $anyExpiring = $anyExpiring || $isExpiring;

            $lines[] = $subscription->serviceLabel().' ('.$subscription->name.'): '.($isExpiring
                ? 'expired '.$subscription->end_date->diffForHumans()
                : 'expires '.$subscription->end_date->diffForHumans());
        }

        if (empty($lines)) {
            return $noActivePlan;
        }

        return [
            Stat::make('Subscriptions', count($lines).' active')
                ->description(implode(' · ', $lines))
                ->icon('heroicon-o-credit-card')
                ->color($anyExpiring ? 'warning' : 'success')
                ->url('/user/billing'),
        ];
    }
}
