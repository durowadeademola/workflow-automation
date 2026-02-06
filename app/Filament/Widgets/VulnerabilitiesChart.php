<?php

namespace App\Filament\Widgets;

use App\Models\Vulnerability;
use Filament\Widgets\ChartWidget;

class VulnerabilitiesChart extends ChartWidget
{
    protected ?string $heading = 'Vulnerabilities Trend';

    protected string $color = 'success';

    public static function canView(): bool
    {
       return auth()->check() && auth()->user()?->is_admin;
    }

    protected function getData(): array
    {
        $data = Vulnerability::query()
            ->selectRaw('MONTHNAME(created_at) as label, COUNT(*) as total')
            //->where('status', 'active')
            //->where('client_id', auth()->user()?->client_id)
            ->whereYear('created_at', now()->year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                [
                    'label' => 'Found Vulnerabilities',
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
