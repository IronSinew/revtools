<?php

namespace App\ValueObjects\Regions;

use Spatie\LaravelData\Data;

final class RoomPosition extends Data
{
    public function __construct(
        public int $x,
        public int $y,
        public int $z,
    ) {}
}
