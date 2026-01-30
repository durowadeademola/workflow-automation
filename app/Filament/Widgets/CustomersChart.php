<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;

class CustomersChart extends ChartWidget
{
    protected ?string $heading = 'Customers Growth';

    protected string $color = 'success';

    public static function canView(): bool
    {
        $user = auth()->user();

        /** * We check if the user exists, is a client, and if their
         * associated customer profile has the right type.
         */
        return $user
            && $user->is_client || $user->is_agent
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
        $data = Customer::query()
            ->selectRaw("DATE_FORMAT(created_at, '%b') as label, COUNT(*) as total")
            ->where('client_id', auth()->user()?->client_id)
            ->whereYear('created_at', now()->year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                [
                    'label' => 'Total Customers for '.now()->year,
                    'data' => $data->values(),
                    // 'backgroundColor' => '#10B981',
                    'borderColor' => '#22C55E',
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
