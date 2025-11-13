<?php

namespace App\Enums\Quests;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum QuestRewardType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Faction = 'faction';
    case SpellPoint = 'spell_point';
    case SkillPoint = 'skill_point';
    case StatPoint = 'stat_point';
    case Item = 'item';
    case Title = 'title';
}
