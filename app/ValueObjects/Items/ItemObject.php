<?php

namespace App\ValueObjects\Items;

use App\Enums\Items\ItemSlot;
use App\Enums\Items\ItemSubType;
use App\Enums\Items\ItemType;
use Spatie\LaravelData\Data;

final class ItemObject extends Data
{
    public function __construct(
        public int $external_id,
        public string $name,
        public int $gold_value,
        public ItemType $type,
        public ?ItemSubType $sub_type = null,
        public ?ItemSlot $slot = null,
        public ?int $speed = null,
        public ?int $type_value = null,
        public ?ItemDeprecatedData $deprecated_data = null,
        public ?ItemRequirements $requirements = null,
        /** @var ItemEffect[]|null */
        public ?array $effects = null,
        public ?string $description = null,
    ) {}
}
