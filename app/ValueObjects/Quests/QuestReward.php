<?php

namespace App\ValueObjects\Quests;

use App\Enums\Quests\QuestRewardType;
use Spatie\LaravelData\Data;

final class QuestReward extends Data
{
    public function __construct(
        public QuestRewardType $type,
        public string $name,
        public int $amount,
    ) {}
}
