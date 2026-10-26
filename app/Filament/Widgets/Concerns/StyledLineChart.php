<?php

namespace App\Filament\Widgets\Concerns;

/**
 * Shared Chart.js styling for the dashboard's line charts, so they all get
 * the same smooth curve, soft fill, ringed points and quiet grid instead of
 * Chart.js's straight-line defaults.
 */
trait StyledLineChart
{
    /**
     * @param  array<string, mixed>  $dataset
     * @return array<string, mixed>
     */
    protected function styleDataset(array $dataset, string $hex): array
    {
        [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');

        return [
            'borderColor' => $hex,
            'backgroundColor' => "rgba({$r}, {$g}, {$b}, 0.10)",
            'borderWidth' => 2.5,
            'tension' => 0.4,
            'fill' => 'start',
            'pointRadius' => 4,
            'pointHoverRadius' => 6,
            'pointBackgroundColor' => '#ffffff',
            'pointBorderColor' => $hex,
            'pointBorderWidth' => 2,
            ...$dataset,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function styledOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'pointStyle' => 'circle',
                        'boxWidth' => 8,
                        'boxHeight' => 8,
                        'padding' => 16,
                    ],
                ],
                'tooltip' => [
                    'padding' => 10,
                    'cornerRadius' => 8,
                    'displayColors' => false,
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => ['display' => false],
                    'border' => ['display' => false],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'border' => ['display' => false],
                    'grid' => ['color' => 'rgba(148, 163, 184, 0.15)'],
                    'ticks' => [
                        'stepSize' => 1,
                        'precision' => 0,
                        'padding' => 8,
                    ],
                ],
            ],
        ];
    }
}
