<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Enums\Role;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'fas-user-gear';

    protected static ?string $navigationGroup = 'Filament Shield';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Section::make('User Details')
                            ->description('Fill in the basic user details.')
                            ->icon('heroicon-o-user')
                            ->collapsible()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Full Name')
                                    ->placeholder('Enter full name')
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Please enter the user\'s full name'),

                                Forms\Components\TextInput::make('email')
                                    ->label('Email Address')
                                    ->placeholder('Enter email address')
                                    ->email()
                                    ->unique()
                                    ->required()
                                    ->maxLength(255)
                                    ->helperText('Ensure this email is unique'),

                                Forms\Components\TextInput::make('password')
                                    ->label('Password')
                                    ->password()
                                    ->nullable()
                                    ->maxLength(255)
                                    ->helperText('Leave blank to retain the current password'),
                            ]),

                        Forms\Components\Section::make('Role Details')
                            ->description('Assign a role to the user.')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Select::make('role')
                                    ->label('Assign Role')
                                    ->preload()
                                    ->options(Role::class)
                                    ->native(false)
                                    ->placeholder('Select Role')
                                    ->required()
                                    ->searchable(),
                            ]),

                    ]),
            ]);
    }

    /**
     * @throws \Exception
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                Tables\Filters\TrashedFilter::make(),
                Tables\Filters\SelectFilter::make('roles')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->multiple()
                    ->preload(),
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
            //
        ];
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('User Overview')
                    ->icon('heroicon-o-user-circle')
                    ->description('Overview of user profile and roles.')
                    ->collapsible()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                ImageEntry::make('avatar')
                                    ->label('Profile Picture')
                                    ->circular()
                                    ->height(120)
                                    ->columnSpanFull(),

                                TextEntry::make('name')
                                    ->label('Full Name')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->badge()
                                    ->color('primary')
                                    ->formatStateUsing(fn (string $state) => ucfirst($state)),

                                TextEntry::make('email')
                                    ->label('Email Address')
                                    ->size('sm')
                                    ->badge()
                                    ->color('success'),
                            ]),
                    ]),

                Section::make('Role Information')
                    ->icon('heroicon-o-shield-check')
                    ->description('User roles and permissions.')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('roles.name')
                                    ->label('Assigned Roles')
                                    ->badge()
                                    ->color('info')
                                    ->separator(', ')
                                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                                    ->weight('medium')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScope(
                scope: SoftDeletingScope::class
            );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'view' => Pages\ViewUser::route('/{record}'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
