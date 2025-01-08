<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\EditTenantProfile;
use Illuminate\Support\Str;

class EditTeamProfile extends EditTenantProfile
{
    public static function getLabel(): string
    {
        return 'Team Profile';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                Section::make('Team Details')
                                    ->icon('heroicon-o-users')
                                    ->description('Manage your team information and settings.')
                                    ->schema([
                                        TextInput::make('name')
                                            ->label('Team Name')
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->minLength(2)
                                            ->maxLength(30)
                                            ->placeholder('Enter team name')
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))
                                            ),

                                        TextInput::make('slug')
                                            ->label('Team URL')
                                            ->required()
                                            ->unique(Team::class, ignoreRecord: true)
                                            ->disabled()
                                            ->dehydrated()
                                            ->helperText('This URL will be used to access your team workspace.'),
                                    ]),

                                Section::make('Danger Zone')
                                    ->icon('heroicon-o-exclamation-triangle')
                                    ->description('Be careful with these actions.')
                                    ->schema([
                                        // TODO: Transfer ownership, delete team options can go here
                                    ])
                                    ->collapsible(),
                            ])
                            ->columnSpan(['lg' => 2]),

                        Section::make()
                            ->schema([
                                Section::make('Team Stats')
                                    ->icon('heroicon-o-chart-bar')
                                    ->schema([
                                        // TODO: Member count, created date etc.
                                    ]),
                            ])
                            ->columnSpan(['lg' => 1]),
                    ])
                    ->columns(3),
            ]);
    }
}
