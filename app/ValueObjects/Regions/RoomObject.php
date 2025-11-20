<?php

namespace App\ValueObjects\Regions;

use App\Enums\Regions\RoomColor;
use App\Enums\Regions\RoomExitType;
use Spatie\LaravelData\Data;

final class RoomObject extends Data
{
    public function __construct(
        public string $name,
        public string $description,
        public string $color_code,
        public string $terrain_color,
        public RoomPosition $coordinates,
        public int $region_id,
        public ?int $exit_region_id = null,
        /** @var RoomExitType[] */
        public ?array $exits = null,
        /** @var string[] */
        public ?array $npcs = null,
    ) {}
}
