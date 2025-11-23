<?php

namespace App\ValueObjects\Regions;

use Spatie\LaravelData\Data;

final class RegionObject extends Data
{
    public function __construct(
        public string $name,
    ) {}
}
