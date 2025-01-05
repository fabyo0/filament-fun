<?php

namespace App\Filament\Resources\EmployeeRelationResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EmployeeRelationManager extends RelationManager
{
    protected static string $relationship = 'employee';

    public function form(Form $form): Form
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
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('first_name')
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('date_hired')
                    ->date()
                    ->sortable(),

                Tables\Columns\TextColumn::make('country.name')
                    ->badge()
                    ->searchable(),

            ])->defaultSort(column: 'first_name', direction: 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('country')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Country'),

                Tables\Filters\Filter::make('date_hired')
                    ->label('Filter by Hire Date'),
            ])
            ->headerActions([
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
