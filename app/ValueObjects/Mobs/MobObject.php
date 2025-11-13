<?php

namespace App\ValueObjects\Mobs;

use App\Enums\Mobs\MobTier;
use App\Enums\Mobs\MobType;
use Spatie\LaravelData\Data;

final class MobObject extends Data
{
    public function __construct(
        public int $external_id,
        public string $name,
        public int $level,
        public MobType $type,
        public MobTier $tier,
        /** @var string[]|null */
        public ?array $location = null,
        public ?MobDeprecatedData $deprecated_data = null,
        /** @var string[]|null */
        public ?array $drops = null,
    ) {}
}
