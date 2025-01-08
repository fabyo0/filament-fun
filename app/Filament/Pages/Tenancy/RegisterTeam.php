<?php

declare(strict_types=1);

namespace App\Filament\Pages\Tenancy;

use App\Models\Team;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Tenancy\RegisterTenant;
use Illuminate\Support\Str;

class RegisterTeam extends RegisterTenant
{
    /* public static function getLabel(): string
     {
         return 'Register Team';
     }

     public function form(Form $form): Form
     {
         return $form
             ->schema([
                 Section::make('Team Information')
                     ->icon('heroicon-o-users')
                     ->columns(1)
                     ->description('Create a new team to organize your workspace.')
                     ->schema([
                         TextInput::make('name')
                             ->label('Team Name')
                             ->required()
                             ->unique()
                             ->minLength(2)
                             ->maxLength(30)
                             ->placeholder('Enter team name')
                             ->live(onBlur: true)
                             ->afterStateUpdated(fn(Set $set, ?string $state) => $set('slug', Str::slug($state)))
                             ->columnSpanFull(),

                         TextInput::make('slug')
                             ->label('Team URL')
                             ->required()
                             ->unique(Team::class)
                             ->disabled()
                             ->dehydrated()
                             ->helperText('This URL will be used to access your team workspace.')
                             ->columnSpanFull(),
                     ])
                     ->columns(1)
                     ->statePath('data')
             ]);
     }*/

    public static function getLabel(): string
    {
        return 'Register team';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug($state))
                    ),
                TextInput::make('slug')
                    ->required()
                    ->unique(Team::class)
                    ->disabled()
                    ->dehydrated(),
            ])
            ->statePath('data');
    }

    protected function handleRegistration(array $data): Team
    {
        $team = Team::create($data);
        $team->addMember(auth()->user());

        return $team;
    }
}
