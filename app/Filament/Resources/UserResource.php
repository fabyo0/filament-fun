<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use Spatie\Permission\Models\Role;

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
                                    ->helperText('Upload a profile picture (Max size: 5MB).'),
                            ]),

                        Forms\Components\Section::make('Role Details')
                            ->description('Assign a role to the user.')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Forms\Components\Select::make('roles')
                                    ->label('Assign Role')
                                    ->preload()
                                    ->options(Role::pluck('name', 'name')->toArray())
                                    ->placeholder('Select Role')
                                    ->native(false)
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
                    ->label('Full Name')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-user')
                    ->weight('bold')
                    ->description(fn ($record) => $record->email),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('User Roles')
                    ->badge()
                    ->color('primary')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->icon('heroicon-o-shield-check')
                    ->formatStateUsing(fn ($state) => ucfirst($state)),

                Tables\Columns\IconColumn::make('email_verified_at')
                    ->label('Email Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime()
                    ->sortable()
                    ->toggleable()
                    ->description(fn ($record) => $record->last_login_ip)
                    ->icon('heroicon-o-clock')
                    ->since(),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Deleted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-trash')
                    ->color('danger'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registration Date')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\TrashedFilter::make()
                    ->label('Show Deleted Users')
                    ->indicator('Deleted'),

                Tables\Filters\SelectFilter::make('roles')
                    ->relationship(name: 'roles', titleAttribute: 'name')
                    ->multiple()
                    ->preload()
                    ->label('Filter by Role')
                    ->indicator('Roles'),

                Filter::make('verified')
                    ->label('Email Verification')
                    ->form([
                        Select::make('email_verified')
                            ->options([
                                'verified' => 'Verified Users',
                                'unverified' => 'Unverified Users',
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['email_verified'],
                            fn (Builder $query, $status): Builder => match ($status) {
                                'verified' => $query->whereNotNull('email_verified_at'),
                                'unverified' => $query->whereNull('email_verified_at'),
                                default => $query
                            }
                        );
                    }),

                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from')
                            ->label('Registered From'),
                        DatePicker::make('created_until')
                            ->label('Registered Until'),
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
            ->filtersFormColumns(3)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->icon('heroicon-o-eye'),

                Tables\Actions\EditAction::make()
                    ->icon('heroicon-o-pencil'),

                Tables\Actions\DeleteAction::make()
                    ->icon('heroicon-o-trash'),

                Tables\Actions\RestoreAction::make()
                    ->icon('heroicon-o-arrow-path'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    ExportBulkAction::make(),
                ]),
            ])
            ->striped()
            ->poll('60s');
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

    public static function getNavigationBadge(): ?string
    {
        return (string) User::count();
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
