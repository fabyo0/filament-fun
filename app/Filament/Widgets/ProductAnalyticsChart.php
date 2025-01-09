<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ProductAnalyticsChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'productAnalytics';

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Product Distribution';

    protected static ?int $sort = 2;

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     */
    protected function getOptions(): array
    {
        $products = Product::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'chart' => [
                'type' => 'line',
                'height' => 300,
                'toolbar' => ['show' => false],
            ],
            'series' => [
                [
                    'name' => 'New Products',
                    'data' => $products->pluck('total')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $products->pluck('date')->toArray(),
                'labels' => ['style' => ['colors' => '#9ca3af']],
            ],
            'stroke' => ['curve' => 'smooth'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'opacityFrom' => 0.7,
                    'opacityTo' => 0.3,
                ],
            ],
        ];
    }
}
