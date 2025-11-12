<?php

namespace App\ValueObjects\Items;

use App\Enums\Items\ItemEffectType;
use Spatie\LaravelData\Data;

final class ItemEffect extends Data
{
    public function __construct(
        public ?ItemEffectType $type,
        public ?string $name = null,
        public ?int $level = null,
        public ?int $recharge_time = null,
        public ?int $value = null,
        /** @var string[] */
        public ?array $descriptions = null,
        // only used with procs
        public ?string $trigger = null,
    ) {}
}
