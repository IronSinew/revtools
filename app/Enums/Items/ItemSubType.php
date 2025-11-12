<?php

namespace App\Enums\Items;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum ItemSubType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    /**
     * Armor Types (TODO: add attribute group)
     */
    case StuddedLeather = 'studded_leather';
    case Chainmail = 'chainmail';
    case Leather = 'leather';
    case Cloth = 'cloth';
    case Jewelry = 'jewelry';
    case LightPlate = 'light_plate';
    case HeavyPlate = 'heavy_plate';

    /**
     * Weapon Types (TODO: Add group type)
     */
    case Swords = 'swords';
    case Maces = 'maces';
    case Daggers = 'daggers';
    case Hammers = 'hammers';
    case Bows = 'bows';
    case Staffs = 'staffs';

    /**
     * Shield Types (TODO: Add group type)
     */
    case SmallShield = 'small_shield';
    case MediumShield = 'medium_shield';
    case LargeShield = 'large_shield';
    case HugeShield = 'huge_shield';
    case JewelryWand = 'jewelry_wand';

    case Potion = 'potion';
    case Poison = 'poison';
    case Scroll = 'scroll';
}
