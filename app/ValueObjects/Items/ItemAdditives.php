<?php

namespace App\ValueObjects\Items;

use Spatie\LaravelData\Data;

final class ItemAdditives extends Data
{
    public function __construct(
        public ?int $damage = null,
        public ?int $armor = null,
        public ?int $health = null,
        public ?int $mana = null,
    ) {}
}
