<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected static bool $isLazy = false;

    // See AdminStats — Filament's default 5s auto-poll on chart widgets was
    // keeping this page in a constant background-refresh loop.
    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Orders Trend';

    protected string $color = 'primary';

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) $user && ($user->is_client || $user->is_agent);
    }

    protected function getData(): array
    {
        $year = now()->year;

        $data = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%b') as label, COUNT(*) as total")
            ->where('client_id', auth()->user()?->client_id)
            ->whereYear('created_at', $year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                [
                    'label' => "Total Orders for {$year}",
                    'data' => $data->values(),
                    'borderColor' => '#3b82f6',
                    'fill' => 'start',
                    'tension' => 0.3,
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
