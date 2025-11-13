<?php

namespace App\Enums\Mobs;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;
use Illuminate\Support\Str;

enum MobType: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Npc = 'npc';
    case Normal = 'normal';
    case Boss = 'boss';
    case Shopkeeper = 'shopkeeper';
    case AaTrainer = 'aa_trainer';
    case Faction = 'faction';

    /** @codeCoverageIgnore  */
    public function label(): string
    {
        return match ($this) {
            self::AaTrainer => 'AA Trainer',
            default => Str::headline(Str::lower($this->name))
        };
    }

    public static function toEnum(string $value): self
    {
        return match (\Str::snake(strtolower($value))) {
            'player_greeting',  'player_greetings', 'out_of_money' => self::Npc,
            'player_enter_shop', 'shopkeeper', 'bought_item', 'sold_item' => self::Shopkeeper,
            'boss' => self::Boss,
            'aatrainer' => self::AaTrainer,
            '#factionreq' => self::Faction,
            default => self::Normal,
        };
    }
}
