<?php

namespace App\Enums\Regions;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum RoomExitType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case North = 'north';
    case South = 'south';
    case East = 'east';
    case West = 'west';
    case Up = 'up';
    case Down = 'down';

    public static function fromCrapValue(int $value): self
    {
        return match ($value) {
            0 => self::North,
            1 => self::South,
            2 => self::East,
            3 => self::West,
            4 => self::Up,
            5 => self::Down,
            default => self::North,
        };
    }
}
