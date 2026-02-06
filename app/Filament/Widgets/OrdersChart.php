<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Orders Trend';

    protected string $color = 'primary';

    public static function canView(): bool
    {
        $user = auth()->user();

        /** * We check if the user exists, is a client, and if their
         * associated customer profile has the right type.
         */
        return $user && $user->is_client || $user->is_agent
            && in_array(strtolower($user->client?->type), [
                'online-store',
                'real-estate',
                'logistics',
                'sme',
                'ecommerce',
            ]);
    }

    protected function getData(): array
    {
        $data = Order::query()
            ->selectRaw("DATE_FORMAT(created_at, '%b') as label, COUNT(*) as total")
            ->where('client_id', auth()->user()?->client_id)
            ->whereYear('created_at', '2025')
            //->whereYear('created_at', now()->year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                [
                    'label' => 'Total Orders for 2025',
                    'data' => $data->values(),
                    // 'backgroundColor' => '#3b82f6',
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
