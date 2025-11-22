<?php

namespace App\ValueObjects\Regions;

use Spatie\LaravelData\Data;

final class RegionPosition extends Data
{
    public function __construct(
        public int $x,
        public int $y,
        public int $z,
    ) {}
}
