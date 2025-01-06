<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\RelationManagers\CityRelationManager;
use App\Filament\Resources\EmployeeRelationResource\RelationManagers\EmployeeRelationManager;
use App\Filament\Resources\StateResource\Pages;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StateResource extends Resource
{
    protected static ?string $model = State::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationLabel = 'State';

    protected static ?string $navigationGroup = 'Locations';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make('State Details')
                            ->description('Fill in the details about the state.')
                            ->icon('heroicon-o-map')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Select::make('country_id')
                                            ->label('Country')
                                            ->relationship('country', 'name')
                                            ->placeholder('Select a country')
                                            ->native(false)
                                            ->preload()
                                            ->searchable()
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->label('State Name')
                                            ->placeholder('Enter the state name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('country.name')
                    ->sortable()
                    ->searchable(self::$isGloballySearchable = false)
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])->defaultSort(column: 'country.name', direction: 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('State Information')
                    ->icon('heroicon-o-map')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name')
                                    ->label('Country Name')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('name')
                                    ->label('State Name')
                                    ->weight('bold'),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            EmployeeRelationManager::class,
            CityRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStates::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }
}
