<?php

namespace App\Filament\Widgets;

use App\Models\Subscription;
use Filament\Widgets\ChartWidget;

class SubscriptionsChart extends ChartWidget
{
    protected static bool $isLazy = false;

    // See AdminStats — Filament's default 5s auto-poll on chart widgets was
    // keeping this page in a constant background-refresh loop.
    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Subscriptions Trend';

    protected string $color = 'primary';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()?->is_admin;
    }

    protected function getData(): array
    {
        $year = now()->year;

        // Trials aren't a purchase decision, so they're excluded here —
        // this chart is meant to track paid conversions/growth, not signups.
        $data = Subscription::query()
            ->selectRaw('MONTHNAME(created_at) as label, COUNT(*) as total')
            ->where('plan', '!=', 'trial')
            ->whereYear('created_at', $year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                [
                    'label' => "New paid subscriptions for {$year}",
                    'data' => $data->values(),
                    'fill' => 'start',
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
