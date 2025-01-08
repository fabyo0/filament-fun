<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DepartmentResource\Pages;
use App\Filament\Resources\EmployeeRelationResource\RelationManagers\EmployeeRelationManager;
use App\Models\Department;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static ?string $navigationIcon = 'fas-building-circle-arrow-right';

    protected static ?string $navigationLabel = 'Department';

    protected static ?string $modelLabel = 'Personal Department';

    protected static ?string $navigationGroup = 'Department';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Department Section with description
                Forms\Components\Section::make('Department')
                    ->description('Fill in the department details below.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Department Name')
                            ->placeholder('Enter department name')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Enter a unique name for the department.')
                            ->columnSpan(2)
                            ->autofocus(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Department Name')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold')
                    ->icon('heroicon-o-building-office'),

                Tables\Columns\TextColumn::make('employee_count')
                    ->counts('employee')
                    ->label('Team Size')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 10 => 'success',
                        $state >= 5 => 'warning',
                        default => 'danger',
                    })
                    ->icon('heroicon-o-users')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->since()
                    ->sortable()
                    ->icon('heroicon-o-clock')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name', 'asc')
            ->filters([
                Tables\Filters\SelectFilter::make('employee_count')
                    ->options([
                        'small' => 'Small Team (< 5)',
                        'medium' => 'Medium Team (5-10)',
                        'large' => 'Large Team (> 10)',
                    ])
                    ->query(function (Builder $query, array $data) {
                        return match ($data['value']) {
                            'small' => $query->having('employee_count', '<', 5),
                            'medium' => $query->having('employee_count', '>=', 5)
                                ->having('employee_count', '<=', 10),
                            'large' => $query->having('employee_count', '>', 10),
                            default => $query
                        };
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmployeeRelationManager::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) Department::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDepartments::route('/'),
            'create' => Pages\CreateDepartment::route('/create'),
            //            'view' => Pages\ViewDepartment::route('/{record}'),
            'edit' => Pages\EditDepartment::route('/{record}/edit'),
        ];
    }
}
