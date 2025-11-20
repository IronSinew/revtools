<?php

namespace App\Enums;

use App\Enums\Items\ItemSubType;
use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum ClassType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Assassin = 'assassin';
    case Necromancer = 'necromancer';
    case Redeemer = 'redeemer';
    case Priest = 'priest';
    case Defender = 'defender';
    case Paladin = 'paladin';
    case Ranger = 'ranger';
    case Warrior = 'warrior';
    case Druid = 'druid';
    case Wizard = 'wizard';
    case Enchanter = 'enchanter';
    case Summoner = 'summoner';

    /**
     * @return ItemSubType[]
     */
    public function usableSubTypes(): array
    {
        $wearables = match ($this) {
            self::Warrior => [
                ItemSubType::Swords, ItemSubType::Cloth, ItemSubType::Leather, ItemSubType::SmallShield,
                ItemSubType::StuddedLeather, ItemSubType::Chainmail, ItemSubType::MediumShield, ItemSubType::LightPlate,
            ],
            self::Defender => [
                ItemSubType::Swords, ItemSubType::Cloth, ItemSubType::Leather, ItemSubType::StuddedLeather,
                ItemSubType::Chainmail, ItemSubType::LightPlate, ItemSubType::HeavyPlate,
                ItemSubType::SmallShield, ItemSubType::MediumShield, ItemSubType::LargeShield, ItemSubType::HugeShield,
            ],
            self::Assassin => [
                ItemSubType::Daggers, ItemSubType::Cloth, ItemSubType::Leather, ItemSubType::Poison,
            ],
            self::Priest => [
                ItemSubType::Maces, ItemSubType::Cloth, ItemSubType::Leather, ItemSubType::StuddedLeather,
                ItemSubType::Chainmail,
            ],
            self::Paladin => [
                ItemSubType::Hammers, ItemSubType::Cloth, ItemSubType::Leather, ItemSubType::StuddedLeather,
                ItemSubType::Chainmail, ItemSubType::LightPlate, ItemSubType::HeavyPlate,
                ItemSubType::SmallShield, ItemSubType::MediumShield, ItemSubType::LargeShield, ItemSubType::HugeShield,
            ],
            self::Redeemer => [
                ItemSubType::Staffs, ItemSubType::Cloth, ItemSubType::Leather,
            ],
            self::Wizard, self::Summoner, self::Enchanter => [
                ItemSubType::Daggers, ItemSubType::Cloth,
            ],
            self::Druid => [
                ItemSubType::Maces, ItemSubType::Cloth, ItemSubType::Leather, ItemSubType::StuddedLeather,
            ],
            self::Ranger => [
                ItemSubType::Swords, ItemSubType::Bows, ItemSubType::Cloth, ItemSubType::Leather, ItemSubType::StuddedLeather,
            ],
            self::Necromancer => [
                ItemSubType::Staffs, ItemSubType::Cloth, ItemSubType::Poison,
            ]
        };

        return [...$wearables, ...[ItemSubType::Potion, ItemSubType::Scroll, ItemSubType::JewelryWand, ItemSubType::Jewelry]];
    }
}
