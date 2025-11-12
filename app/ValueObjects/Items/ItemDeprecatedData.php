<?php

namespace App\ValueObjects\Items;

use Spatie\LaravelData\Data;

final class ItemDeprecatedData extends Data
{
    public function __construct(
        public ?int $stamina = null,
        public ?int $duration = null,
        public ?int $weight = null,
    ) {}
}
