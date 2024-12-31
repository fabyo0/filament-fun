<?php

declare(strict_types=1);

namespace App\Enums;

enum Role: string
{
    case ADMIN = 'Admin';
    case USER = 'User';

    public function label(): string
    {
        return strtolower($this->value);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function fromLabel(string $label): self
    {
        return match (strtolower($label)) {
            'admin' => self::ADMIN,
            'user' => self::USER,
            default => throw new \ValueError("Invalid label: $label")
        };
    }
}
