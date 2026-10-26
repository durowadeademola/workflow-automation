<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Filament\Widgets\Concerns\StyledLineChart;
use Filament\Widgets\ChartWidget;

class ClientsChart extends ChartWidget
{
    use StyledLineChart;

    protected static bool $isLazy = false;

    // See AdminStats — Filament's default 5s auto-poll on chart widgets was
    // keeping this page in a constant background-refresh loop.
    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Clients Trend';

    protected string $color = 'success';

    public static function canView(): bool
    {
        return auth()->check() && auth()->user()?->is_admin;
    }

    protected function getData(): array
    {
        $data = Client::query()
            ->selectRaw('MONTHNAME(created_at) as label, COUNT(*) as total')
            ->where('status', 'active')
            // ->where('client_id', auth()->user()?->client_id)
            // ->whereYear('created_at', now()->year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                $this->styleDataset([
                    'label' => 'Active clients',
                    'data' => $data->values(),
                ], '#2563eb'),
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
        return $this->styledOptions();
    }
}
