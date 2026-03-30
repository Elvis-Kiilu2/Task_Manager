<?php

namespace App\Enums;

enum Priority: string
{
    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';

    public function weight(): int
    {
        return match ($this) {
            self::HIGH => 3,
            self::MEDIUM => 2,
            self::LOW => 1,
        };
    }
}
