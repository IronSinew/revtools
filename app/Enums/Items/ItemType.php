<?php

namespace App\Enums\Items;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum ItemType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Weapon = 'weapon';
    case Armor = 'armor';
    case Usable = 'usable';
}
