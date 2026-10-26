<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Filament\Widgets\Concerns\StyledLineChart;
use Filament\Widgets\ChartWidget;

class OrdersChart extends ChartWidget
{
    use StyledLineChart;

    protected static bool $isLazy = false;

    // See AdminStats — Filament's default 5s auto-poll on chart widgets was
    // keeping this page in a constant background-refresh loop.
    protected ?string $pollingInterval = null;

    protected ?string $heading = 'Appointments Booked Trend';

    protected string $color = 'primary';

    public static function canView(): bool
    {
        $user = auth()->user();

        return (bool) $user && ($user->is_client || $user->is_agent);
    }

    protected function getData(): array
    {
        $year = now()->year;

        $data = Appointment::query()
            ->selectRaw("DATE_FORMAT(created_at, '%b') as label, COUNT(*) as total")
            ->where('client_id', auth()->user()?->client_id)
            ->whereYear('created_at', $year)
            ->groupBy('label')
            ->orderByRaw('MIN(created_at)')
            ->pluck('total', 'label');

        return [
            'datasets' => [
                $this->styleDataset([
                    'label' => "Appointments Booked in {$year}",
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
