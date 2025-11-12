<?php

namespace App\Enums\Items;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum ItemEffectType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Spell = 'spell';
    case Potion = 'potion';
    case Poison = 'poison';
    case Scroll = 'scroll';
    case Proc = 'proc';
    case Generic = 'generic';
}
