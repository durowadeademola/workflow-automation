<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;

class CustomersChart extends ChartWidget
{
    protected ?string $heading = 'New Customers';

    protected string $color = 'success';

    protected function getData(): array
    {
        $data = Customer::query()
            ->selectRaw('MONTHNAME(created_at) as label, COUNT(*) as total')
            ->where('client_id', auth()->user()?->client_id)
            ->whereYear('created_at', now()->year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                [
                    'label' => 'New Customers this Year',
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
}
