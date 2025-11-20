<?php

namespace App\ValueObjects\Items;

use App\Enums\ClassType;
use Spatie\LaravelData\Data;

final class ItemRequirements extends Data
{
    public function __construct(
        public ?int $level = null,
        /** @var ClassType[] */
        public ?array $classes = null,
        public ?string $skill = null,
        public ?int $skill_level = null,
        public ?int $quest = null,
        public ?int $strength = null,
        public ?int $agility = null,
        public ?int $intelligence = null,
        public ?int $focus = null,
        public ?int $toughness = null,
        public ?int $charisma = null,
        public ?int $aa_level = null,
        public ?string $spell = null,
        public ?int $spell_level = null,
        public bool $classes_are_inferred = false,
    ) {}
}
