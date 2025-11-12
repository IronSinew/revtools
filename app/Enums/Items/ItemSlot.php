<?php

namespace App\Enums\Items;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum ItemSlot: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Shield = 'shield';
    case Hands = 'hands';
    case Feet = 'feet';
    case Head = 'head';
    case Wrist = 'wrist';
    case Chest = 'chest';
    case Legs = 'legs';
    case Finger = 'finger';
    case Neck = 'neck';
    case Potion = 'potion';
    case Poison = 'poison';
    case Scroll = 'scroll';
}
