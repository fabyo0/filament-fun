<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\City;
use App\Models\Employee;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'fas-users-gear';

    protected static ?string $navigationGroup = 'Department';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // User Detail Section
                Forms\Components\Section::make('User Information')
                    ->description('Fill out the user\'s personal details and department information.')
                    ->icon('heroicon-o-user')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->label('First Name')
                                    ->placeholder('Enter first name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Enter the first name of the user.'),

                                Forms\Components\TextInput::make('last_name')
                                    ->label('Last Name')
                                    ->placeholder('Enter last name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Enter the last name of the user.'),

                                Forms\Components\TextInput::make('middle_name')
                                    ->label('Middle Name')
                                    ->placeholder('Enter middle name')
                                    ->maxLength(255)
                                    ->helperText('Enter middle name if applicable.'),

                                Forms\Components\Select::make('department_id')
                                    ->label('Department')
                                    ->relationship('department', 'name')
                                    ->placeholder('Select Department')
                                    ->preload()
                                    ->native(false)
                                    ->searchable()
                                    ->required()
                                    ->helperText('Select the department the user belongs to.'),
                            ]),
                        Forms\Components\FileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->image()
                            ->maxSize(1024)
                            ->directory('avatars')
                            ->preserveFilenames()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                            ->helperText('Upload a profile picture (Max size: 1MB).'),
                    ]),

                // Address Detail Section
                Forms\Components\Section::make('Address Details')
                    ->description('Provide the user\'s address and location information.')
                    ->icon('heroicon-o-map')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('country_id')
                                    ->label('Country')
                                    ->relationship('country', 'name')
                                    ->placeholder('Select Country')
                                    ->searchable()
                                    ->afterStateUpdated(function (Set $set) {
                                        $set('state_id', null);
                                        $set('city_id', null);
                                    })
                                    ->live()
                                    ->preload()
                                    ->native(false)
                                    ->required()
                                    ->helperText('Select the country where the user resides.'),

                                Forms\Components\Select::make('state_id')
                                    ->label('State')
                                    ->options(function (Get $get): Collection {
                                        return State::query()->where('country_id', $get('country_id'))
                                            ->pluck('name', 'id');
                                    })
                                    ->placeholder('Select State')
                                    ->afterStateUpdated(fn (Set $set) => $set('city_id', null))
                                    ->native(false)
                                    ->preload()
                                    ->live()
                                    ->searchable()
                                    ->disabled(fn (Get $get): bool => ! $get('country_id'))
                                    ->required()
                                    ->helperText('Select the state within the country.'),

                                Forms\Components\Select::make('city_id')
                                    ->label('City')
                                    ->options(function (Get $get): Collection {
                                        return City::query()->where('state_id', $get('state_id'))
                                            ->pluck('name', 'id');
                                    })
                                    ->placeholder('Select City')
                                    ->searchable()
                                    ->live()
                                    ->disabled(fn (Get $get): bool => ! $get('state_id'))
                                    ->required()
                                    ->helperText('Select the city within the selected state.'),

                                Forms\Components\Textarea::make('address')
                                    ->label('Street Address')
                                    ->placeholder('Enter street address')
                                    ->maxLength(255)
                                    ->helperText('Enter the street address for the user.'),

                                Forms\Components\TextInput::make('zip_code')
                                    ->label('ZIP Code')
                                    ->placeholder('Enter ZIP code')
                                    ->maxLength(255)
                                    ->numeric()
                                    ->helperText('Enter the user\'s ZIP code.'),
                            ]),
                    ]),

                // Date Hired Section
                Forms\Components\Section::make('Dates')
                    ->description('Provide the date-related information of the user.')
                    ->icon('heroicon-o-calendar')
                    ->collapsible()
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DatePicker::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->displayFormat('d/m/Y')
                                    ->placeholder('Select date of birth')
                                    ->helperText('Select the user\'s date of birth.'),

                                Forms\Components\DatePicker::make('date_hired')
                                    ->label('Date Hired')
                                    ->displayFormat('d/m/Y')
                                    ->placeholder('Select hiring date')
                                    ->required()
                                    ->helperText('Select the date the user was hired.'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Employee Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-user')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('department.name')
                    ->label('Department')
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-o-building-office')
                    ->sortable()
                    ->toggleable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('date_hired')
                    ->label('Hire Date')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-o-calendar')
                    ->description(fn ($record) => 'Service: '.Carbon::parse($record->date_hired)->diffForHumans(null, true))
                    ->toggleable(),

            ])
            ->defaultSort('first_name', 'desc')
            ->filters([
                SelectFilter::make('department')
                    ->label('Department')
                    ->native(false)
                    ->searchable()
                    ->preload()
                    ->multiple()
                    ->relationship(
                        name: 'department',
                        titleAttribute: 'name'
                    )
                    ->indicator('Department'),

                Filter::make('hire_date')
                    ->form([
                        DatePicker::make('hired_from')
                            ->label('Hired From')
                            ->displayFormat('d/m/Y'),
                        DatePicker::make('hired_until')
                            ->label('Hired Until')
                            ->displayFormat('d/m/Y'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['hired_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_hired', '>=', $date),
                            )
                            ->when(
                                $data['hired_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('date_hired', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['hired_from'] ?? null) {
                            $indicators[] = 'Hired from '.Carbon::parse($data['hired_from'])->toFormattedDateString();
                        }

                        if ($data['hired_until'] ?? null) {
                            $indicators[] = 'Hired until '.Carbon::parse($data['hired_until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
            ])
            ->filtersFormColumns(2)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),
                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ])
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Detail')
                    ->icon('heroicon-o-user')
                    ->description('Employee personal information and department details.')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->label('Profile Image')
                                    ->circular()
                                    ->height(100)
                                    ->columnSpanFull(),

                                TextEntry::make('first_name')
                                    ->label('First Name')
                                    ->weight('bold')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('last_name')
                                    ->label('Last Name')
                                    ->weight('bold')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('middle_name')
                                    ->label('Middle Name')
                                    ->weight('medium')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('department.name')
                                    ->label('Department')
                                    ->icon('heroicon-o-building-office')
                                    ->weight('medium')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                    ]),

                Section::make('Address Detail')
                    ->icon('heroicon-o-map-pin')
                    ->description('Employee location and contact information.')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name')
                                    ->label('Country')
                                    ->icon('heroicon-o-globe-alt')
                                    ->weight('medium')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('state.name')
                                    ->label('State')
                                    ->icon('heroicon-o-map')
                                    ->weight('medium')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('city.name')
                                    ->label('City')
                                    ->icon('heroicon-o-building-office-2')
                                    ->weight('medium')
                                    ->badge()
                                    ->color('primary'),

                                TextEntry::make('zip_code')
                                    ->label('Zip Code')
                                    ->icon('heroicon-o-inbox')
                                    ->weight('medium')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('address')
                                    ->label('Address')
                                    ->icon('heroicon-o-home')
                                    ->columnSpanFull()
                                    ->prose()
                                    ->markdown(),
                            ]),
                    ]),

                Section::make('Date Information')
                    ->icon('heroicon-o-calendar')
                    ->description('Important dates related to the employee.')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('date_of_birth')
                                    ->label('Date of Birth')
                                    ->icon('heroicon-o-cake')
                                    ->date('d/m/Y')
                                    ->badge()
                                    ->color('info'),

                                TextEntry::make('date_hired')
                                    ->label('Date Hired')
                                    ->icon('heroicon-o-briefcase')
                                    ->date('d/m/Y')
                                    ->badge()
                                    ->color('success'),

                                TextEntry::make('created_at')
                                    ->label('Created Date')
                                    ->icon('heroicon-o-document-plus')
                                    ->dateTime('d/m/Y H:i')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('updated_at')
                                    ->label('Last Update')
                                    ->icon('heroicon-o-arrow-path')
                                    ->since()
                                    ->badge()
                                    ->color('warning'),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
