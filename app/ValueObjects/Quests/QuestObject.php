<?php

namespace App\ValueObjects\Quests;

use App\Enums\ClassType;
use App\Enums\Quests\QuestRewardType;
use App\Models\Mob;
use App\Models\Quest;
use App\ValueObjects\Mobs\MobObject;
use Spatie\LaravelData\Data;

final class QuestObject extends Data
{
    public function __construct(
        public string  $name,
        public int $external_id,
        public int     $level,
        /** @var string[] */
        public array   $objectives,
        /** @var string[] */
        public array   $steps,
        public ?int  $mob_id = null,
        /** @var ClassType[] */
        public ?array  $required_class = null,
        /** @var QuestReward[] */
        public ?array  $raw_rewards = null,
        /** @var QuestRewardType[] */
        public ?array $reward_types = null,
    ) {}
}