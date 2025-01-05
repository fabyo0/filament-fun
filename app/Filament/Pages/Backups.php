<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use ShuvroRoy\FilamentSpatieLaravelBackup\Pages\Backups as BaseBackups;

class Backups extends BaseBackups
{
    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Backup Tool';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'General Settings';
    }
}
