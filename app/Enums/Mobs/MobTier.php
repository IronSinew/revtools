<?php

namespace App\Enums\Mobs;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum MobTier: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Normal = 'normal';
    case T1 = 't1';
    case T2 = 't2';
    case T3 = 't3';
    case T4 = 't4';
    case T5 = 't5';

    public static function toEnum(string $value): self
    {
        return match (\Str::snake($value)) {
            't1' => self::T1,
            't2' => self::T2,
            't3' => self::T3,
            't4' => self::T4,
            't5' => self::T5,
            default => self::Normal,
        };
    }
}
