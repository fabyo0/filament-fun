<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class EmployeeTrendChart extends ApexChartWidget
{
    /**
     * Chart Id
     */
    protected static ?string $chartId = 'employeeTrend';

    protected static ?int $sort = 1;

    /**
     * Widget Title
     */
    protected static ?string $heading = 'Employee Growth Trend';

    protected function getOptions(): array
    {
        $employees = Employee::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as date')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'chart' => [
                'type' => 'area',
                'height' => 300,
                'toolbar' => ['show' => false],
            ],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 2,
                ],
            ],
            'series' => [
                [
                    'name' => 'New Employees',
                    'data' => $employees->pluck('total')->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $employees->pluck('date')->toArray(),
                'labels' => ['style' => ['colors' => '#9ca3af']],
            ],
            'stroke' => ['curve' => 'smooth'],
            'fill' => [
                'type' => 'gradient',
                'gradient' => [
                    'shade' => 'dark',
                    'type' => 'vertical',
                    'shadeIntensity' => 0.5,
                    'gradientToColors' => ['#fbbf24'],
                    'inverseColors' => true,
                    'opacityFrom' => 1,
                    'opacityTo' => 1,
                    'stops' => [0, 100],
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'tooltip' => [
                'enabled' => true,
            ],
            'colors' => ['#f59e0b'],
        ];
    }
}
