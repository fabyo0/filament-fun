<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;
    protected static ?string $navigationIcon = 'fas-users-gear';
    protected static ?string $navigationGroup = 'Department';

 public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // User Detail
                Forms\Components\Section::make('User Detail')
                    ->description('Put the username details in.')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('middle_name')
                            ->maxLength(255),

                        Forms\Components\Select::make('department_id')
                            ->label('Select Department')
                            ->relationship('department', 'name')
                            ->placeholder('Select Department')
                            ->preload()
                            ->native(false)
                            ->searchable()
                            ->required(),

                        Forms\Components\FileUpload::make('avatar')
                            ->columnSpanFull()
                            ->label('Profile Image')
                            ->image()
                            ->maxSize(1024)
                            ->directory('avatars')
                            ->preserveFilenames()
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('400')
                            ->imageResizeTargetHeight('400')
                    ])->columns(2),


                // Address Detail
                Forms\Components\Section::make('Address Detail')
                    ->description('Put the address details in.')
                    ->schema([
                        Forms\Components\Select::make('country_id')
                            ->relationship('country', 'name')
                            ->label('Country')
                            ->placeholder('Select Country')
                            ->searchable()
                            ->preload()
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('state_id')
                            ->relationship('state', 'name')
                            ->placeholder('Select State')
                            ->native(false)
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('city_id')
                            ->relationship('city', 'name')
                            ->placeholder('Select City')
                            ->native(false)
                            ->searchable()
                            ->required(),
                        Forms\Components\Textarea::make('address')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('zip_code')
                            ->maxLength(255),
                    ])->columns(2),

                // Date hired
                Forms\Components\Section::make('Date Hired')
                    ->schema([
                        Forms\Components\DatePicker::make('date_of_birth'),
                        Forms\Components\DatePicker::make('date_hired')
                            ->required(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('country.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('avatar')
                    ->searchable(),
                Tables\Columns\TextColumn::make('middle_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->searchable(),
                Tables\Columns\TextColumn::make('zip_code')
                    ->searchable(),
                Tables\Columns\TextColumn::make('date_of_birth')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
