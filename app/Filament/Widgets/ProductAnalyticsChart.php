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
                'type' => 'bar',
                'height' => 260,
                'parentHeightOffset' => 2,
                'stacked' => true,
                'toolbar' => [
                    'show' => false,
                ],
            ],
            'series' => [
                [
                    'name' => 'New Products',
                    'data' => $products->pluck('total')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $products->pluck('date')->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'axisTicks' => [
                    'show' => false,
                ],
                'axisBorder' => [
                    'show' => false,
                ],
            ],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#d97706', '#c2410c'],
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],

            'yaxis' => [
                'offsetX' => -16,
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
                'min' => -200,
                'max' => 300,
                'tickAmount' => 5,
            ],

            'stroke' => [
                'curve' => 'smooth',
                'width' => 1,
                'lineCap' => 'round',
            ],
            'colors' => ['#f59e0b', '#ea580c'],
        ];
    }
}
