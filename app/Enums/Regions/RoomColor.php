<?php

namespace App\Enums\Regions;

use App\Interfaces\HasLabel;
use App\Traits\DefaultLabelsFromEnum;

enum RoomColor: string implements HasLabel
{
    use DefaultLabelsFromEnum;

    case Brown = 'brown';
    case Grey40 = 'grey40';
    case Grey60 = 'grey60';
    case Red = 'red';
    case Blue = 'blue';
    case WetGreen = 'wet_green';
    case Grey70 = 'grey70';
    case Midnight = 'midnight';
    case LusciousPurple = 'luscious_purple';
    case Yellow = 'yellow';
    case White = 'white';
    case BrownRoad = 'brown_road';
    case Tan = 'tan';
    case LightGreen = 'light_green';
    case LightBrown = 'light_brown';
    case DeadGrass = 'dead_grass';
    case DarkGreen = 'dark_green';
    case DarkTurquoise = 'dark_turquoise';
    case BrownGreen = 'brown_green';
    case Black = 'black';
    case LightTurquoise = 'light_turquoise';
    case DarkBlue = 'dark_blue';
    case LightBrownGreen = 'light_brown_green';
    case Orange = 'orange';
    case Gray = 'gray';
    case Swamp = 'swamp';
    case BrightGreen = 'bright_green';
    case RottenMauve = 'rotten_mauve';
    case DarkEarth = 'dark_earth';
    case Purple = 'purple';
    case BrightTurquoise = 'bright_turquoise';
    case Grey80 = 'grey80';
    case Murk = 'murk';
    case BadRoom = 'bad_room';
    case Leviathan = 'leviathan';
    case TheLostArchive = 'the_lost_archive';

    public static function fromCrapValue(string $value): self
    {
        return match ($value) {
            'brown' => self::Brown,
            'grey40' => self::Grey40,
            'grey60' => self::Grey60,
            'red' => self::Red,
            'blue' => self::Blue,
            'WetGreen' => self::WetGreen,
            'grey70' => self::Grey70,
            'Midnight' => self::Midnight,
            'LusciousPurp' => self::LusciousPurple,
            'yellow' => self::Yellow,
            'white' => self::White,
            'brownroad' => self::BrownRoad,
            'Tan' => self::Tan,
            'ltgreen' => self::LightGreen,
            'LightBrown' => self::LightBrown,
            'deadgrass' => self::DeadGrass,
            'DarkGreen', 'dkgreen' => self::DarkGreen,
            'dkturq' => self::DarkTurquoise,
            'browngreen' => self::BrownGreen,
            'black', 'Black' => self::Black,
            'ltturq' => self::LightTurquoise,
            'dkblue' => self::DarkBlue,
            'ltbrowngreen' => self::LightBrownGreen,
            'orange' => self::Orange,
            'gray' => self::Gray,
            'swamp' => self::Swamp,
            'BrightGreen' => self::BrightGreen,
            'RottenMauve' => self::RottenMauve,
            'DarkEarth' => self::DarkEarth,
            'purple' => self::Purple,
            'brightturq' => self::BrightTurquoise,
            'grey80' => self::Grey80,
            'Murk' => self::Murk,
            'bad room' => self::BadRoom,
            'leviathan\'s Abyss' => self::Leviathan,
            'The Lost Archive' => self::TheLostArchive,
            default => self::Black,
        };
    }

    public function toColor(): string
    {
        return match ($this) {
            self::Brown => '#8B4513',
            self::Grey40 => '#666666',
            self::Grey60 => '#999999',
            self::Red => '#FF0000',
            self::Blue => '#0000FF',
            self::WetGreen => '#2E8B57',
            self::Grey70 => '#B3B3B3',
            self::Midnight => '#191970',
            self::LusciousPurple => '#9932CC',
            self::Yellow => '#FFFF00',
            self::White => '#FFFFFF',
            self::BrownRoad => '#8B7355',
            self::Tan => '#D2B48C',
            self::LightGreen => '#90EE90',
            self::LightBrown => '#BC8F8F',
            self::DeadGrass => '#9B870C',
            self::DarkGreen => '#006400',
            self::DarkTurquoise => '#00CED1',
            self::BrownGreen => '#6B8E23',
            self::Black => '#000000',
            self::LightTurquoise => '#AFEEEE',
            self::DarkBlue => '#00008B',
            self::LightBrownGreen => '#BDB76B',
            self::Orange => '#FFA500',
            self::Gray => '#808080',
            self::Swamp => '#4B5320',
            self::BrightGreen => '#00FF00',
            self::RottenMauve => '#8B7D7B',
            self::DarkEarth => '#654321',
            self::Purple => '#800080',
            self::BrightTurquoise => '#00FFFF',
            self::Grey80 => '#CCCCCC',
            self::Murk => '#2F4F4F',
            self::BadRoom => '#687c9c',
            self::Leviathan => '#1C1C1C',
            self::TheLostArchive => '#4A4A4A',
        };
    }
}
