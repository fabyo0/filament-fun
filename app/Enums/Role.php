<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Role: string implements HasLabel
{
    case ADMIN = 'Admin';
    case USER = 'User';

    public static function fromLabel(string $label): self
    {
        return match (strtolower($label)) {
            'admin' => self::ADMIN,
            'user' => self::USER,
            default => throw new \ValueError("Invalid label: $label")
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::USER => 'User',
            self::ADMIN => 'Admin'
        };
    }
}
