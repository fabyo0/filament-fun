<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsAdminOverview extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getCards(): array
    {
        return [
            Stat::make('Total Users', User::count())
                ->description('Active system users')
                ->descriptionIcon('heroicon-m-users')
                ->color('info')
                ->chart([7, 3, 4, 5, 6, 3, 5]),

            Stat::make('Total Employees', Employee::count())
                ->description('Current employees')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success')
                ->chart([8, 5, 7, 8, 6, 9, 8]),

            Stat::make('Products', Product::count())
                ->description('Total products')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('warning')
                ->chart([3, 2, 4, 3, 4, 5, 4]),
        ];
    }
}
