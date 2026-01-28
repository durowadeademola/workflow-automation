<?php

namespace App\Filament\Widgets;

use App\Models\Message;

use Filament\Widgets\ChartWidget;

class MessagesChart extends ChartWidget
{
    protected ?string $heading = 'Messages (Last 7 Days)';

    protected function getData(): array
    {
        $data = Message::query()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->pluck('total', 'date');

        return [
            'datasets' => [
                [
                    'label' => 'Messages',
                    'data' => $data->values(),
                ],
            ],
            'labels' => $data->keys(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
