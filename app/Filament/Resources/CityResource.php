<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CityResource\Pages;
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

class CityResource extends Resource
{
    protected static ?string $model = City::class;

    protected static ?string $navigationIcon = 'fas-city';

    protected static ?string $navigationLabel = 'Cities';

    protected static ?string $navigationGroup = 'System';

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
                    ->searchable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->badge()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
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
            //
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
