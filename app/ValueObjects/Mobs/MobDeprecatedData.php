<?php

namespace App\ValueObjects\Mobs;

use Spatie\LaravelData\Data;

final class MobDeprecatedData extends Data
{
    public function __construct(
        public ?int $health = null,
        public ?int $experience = null,
        public ?int $damage = null,
    ) {}
}
