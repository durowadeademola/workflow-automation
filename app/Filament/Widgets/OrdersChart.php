<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    protected ?string $heading = 'Orders Growth';

    protected function getData(): array
    {
        $data = Order::query()
            ->selectRaw('MONTHNAME(created_at) as month, COUNT(*) as total')
            ->where('client_id', auth()->user()?->client_id)
            ->whereYear('created_at', now()->year)
            ->groupBy('month')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'month');

        return [
            'datasets' => [
                [
                    'label' => 'Total Orders',
                    'data' => $data->values(),
                    'backgroundColor' => '#3b82f6',
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
}
