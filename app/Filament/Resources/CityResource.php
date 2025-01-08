<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
use App\Filament\Resources\EmployeeRelationResource\RelationManagers\EmployeeRelationManager;
use App\Models\City;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'fas-city';

    protected static ?string $navigationLabel = 'Cities';

    protected static ?string $navigationGroup = 'Locations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make('City Information')
                            ->description('Provide the details of the city below.')
                            ->icon('heroicon-o-map')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('state_id')
                                            ->relationship(
                                                name: 'state',
                                                titleAttribute: 'name'
                                            )
                                            ->label('State')
                                            ->placeholder('Select State')
                                            ->native(false)
                                            ->preload()
                                            ->searchable()
                                            ->required()
                                            ->helperText('Choose the state this city belongs to.')
                                            ->prefixIcon('heroicon-o-globe-alt'),

                                        Forms\Components\TextInput::make('name')
                                            ->label('City Name')
                                            ->required()
                                            ->placeholder('Enter city name')
                                            ->maxLength(255)
                                            ->helperText('Enter the official name of the city.')
                                            ->prefixIcon('heroicon-o-building-office'),
                                    ]),
                            ]),
                    ])
                    ->columnSpan('full'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('City Name')
                    ->searchable()
                    ->icon('heroicon-o-map')
                    ->sortable()
                    ->weight('bold')
                    ->copyable()
                    ->tooltip('Full city name'),

                Tables\Columns\TextColumn::make('state.name')
                    ->label('State')
                    ->badge()
                    ->icon('heroicon-o-building-office')
                    ->searchable()
                    ->sortable()
                    ->color('success')
                    ->tooltip('State/Region name'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Creation date'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Update')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip('Last update date'),
            ])
            ->defaultSort(column: 'name', direction: 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('state')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by State'),

                Tables\Filters\SelectFilter::make('country')
                    ->relationship('state.country', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Country'),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->tooltip('View details'),

                Tables\Actions\EditAction::make()
                    ->tooltip('Edit city'),

                Tables\Actions\DeleteAction::make()
                    ->tooltip('Delete city'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->requiresConfirmation(),

                    Tables\Actions\BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray'),
                ]),
            ])
            ->emptyStateHeading('No Cities Found')
            ->emptyStateDescription('Create your first city by clicking the button below.')
            ->emptyStateIcon('heroicon-o-building-office-2');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('City Information')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('state.name')
                                    ->label('State Name')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('name')
                                    ->label('City Name')
                                    ->weight('bold'),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmployeeRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCities::route('/'),
            'create' => Pages\CreateCity::route('/create'),
            'edit' => Pages\EditCity::route('/{record}/edit'),
        ];
    }
}
