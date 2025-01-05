<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeResource;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListEmployees extends ListRecords
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'All' => Tab::make(),
            'Week' => Tab::make()
                ->modifyQueryUsing(callback: fn (Builder $query) => $query->where('date_hired', '>', now()->subWeek())),
            'Month' => Tab::make()
                ->modifyQueryUsing(callback: fn (Builder $query) => $query->where('date_hired', '>', now()->subMonths())),
            'Year' => Tab::make()
                ->modifyQueryUsing(callback: fn (Builder $query) => $query->where('date_hired', '>', now()->subYear())),
        ];
    }
}
