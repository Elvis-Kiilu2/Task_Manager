<?php

namespace App\Enums;

enum Status: string
{
    case PENDING = 'pending';
    case IN_PROGRESS = 'in_progress';
    case DONE = 'done';

    public function allowedTransitions(): array
    {
        return match ($this) {
            self::PENDING => [self::IN_PROGRESS],
            self::IN_PROGRESS => [self::DONE],
            self::DONE => [],
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::IN_PROGRESS => 'In Progress',
            self::DONE => 'Done',
        };
    }
}
