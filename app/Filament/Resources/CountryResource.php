<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\CountryResource\Pages;
use App\Filament\Resources\EmployeeRelationResource\RelationManagers\EmployeeRelationManager;
use App\Filament\Resources\StateResource\RelationManagers\StateRelationManager;
use App\Models\Country;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CountryResource extends Resource
{
    protected static ?string $model = Country::class;

    protected static ?string $navigationIcon = 'fas-flag';

    protected static ?string $navigationLabel = 'Country';

    protected static ?string $navigationGroup = 'System';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make('Country Details')
                            ->description('Provide details about the country.')
                            ->icon('heroicon-o-globe-alt')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->label('Country Name')
                                            ->placeholder('Enter country name')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\TextInput::make('code')
                                            ->label('Country Code')
                                            ->placeholder('Enter country code (e.g., US)')
                                            ->string()
                                            ->maxLength(5),

                                        Forms\Components\TextInput::make('phonecode')
                                            ->label('Phone Code')
                                            ->placeholder('Enter phone code (e.g., +1)')
                                            ->required()
                                            ->numeric()
                                            ->maxLength(5),
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
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->badge(),
                Tables\Columns\TextColumn::make('phonecode'),
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

    public static function getRelations(): array
    {
        return [
            StateRelationManager::class,
            EmployeeRelationManager::class,
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Country Information')
                    ->icon('heroicon-o-globe-alt')
                    ->description('Detailed information about the country.')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Country Name')
                                    ->icon('heroicon-o-flag')
                                    ->weight('bold')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('code')
                                    ->label('Country Code')
                                    ->icon('heroicon-o-identification')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('phonecode')
                                    ->label('Phone Code')
                                    ->icon('heroicon-o-phone')
                                    ->badge()
                                    ->color('warning')
                                    ->formatStateUsing(fn (string $state): string => '+'.$state),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('states_count')
                                    ->label('Total States')
                                    ->state(function ($record) {
                                        return $record->state()->count();
                                    })
                                    ->icon('heroicon-o-map')
                                    ->badge()
                                    ->color('primary'),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCountries::route('/'),
            'create' => Pages\CreateCountry::route('/create'),
            'edit' => Pages\EditCountry::route('/{record}/edit'),
        ];
    }
}
