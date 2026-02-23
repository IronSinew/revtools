<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use Illuminate\Database\Seeder;

class ClassToJson extends Seeder
{
    public function run(): void
    {
        // Read raw data file
        $rawData = file_get_contents(storage_path('app/data/classes.dat')); // Put your raw data in classes.txt
        $blocks = explode('}', $rawData);
        $classes = [];

        foreach ($blocks as $block) {
            $block = trim($block);
            if (! $block) {
                continue;
            }

            $lines = array_map('trim', explode("\n", $block));
            $name = array_shift($lines);
            array_shift($lines); // Remove '{'
            $classes[] = $this->parseClass(ClassType::from(trim(strtolower($name))), $lines);
        }

        // Save to JSON

        \Storage::put('data/Classes.json', json_encode($classes, JSON_PRETTY_PRINT));
        echo 'Parsed '.count($classes)." classes into classes.json\n";
    }

    private function parseClass(ClassType $classType, array $data)
    {
        $idx = 0;
        [$hp, $mana] = array_map('intval', explode(' ', $data[$idx]));
        $idx++;

        [$str, $tog, $agi, $foc, $int, $cha] = array_map('intval', explode(' ', $data[$idx]));
        $idx++;

        // Starting skills
        $numSkills = intval($data[$idx]);
        $idx++;
        $skills = array_slice($data, $idx, $numSkills);
        $idx += $numSkills;

        // Starting spells
        $numSpells = intval($data[$idx]);
        $idx++;
        $spells = [];
        if ($numSpells > 0) {
            $spells = array_slice($data, $idx, $numSpells);
            $idx += $numSpells;
        }

        // Starting weapon
        $idx++;

        // Level up data
        [$hpGain, $hpScale, $dmgGain, $dmgScale, $manaScale, $skillInterval, $spellInterval] =
            array_map('floatval', explode(' ', $data[$idx]));
        $idx++;

        // Skills gain
        $numSkillGain = intval($data[$idx]);
        $idx++;
        $skillGain = [];
        for ($i = 0; $i < $numSkillGain; $i++) {
            $parts = explode(' ', $data[$idx]);
            $skillGain[] = ['name' => $parts[0], 'level_learned' => intval($parts[1]), 'cap' => intval($parts[2])];
            $idx++;
        }

        // Spells gain
        $numSpellGain = intval($data[$idx]);
        $idx++;
        $spellGain = [];
        for ($i = 0; $i < $numSpellGain; $i++) {
            $parts = explode(' ', $data[$idx++]);
            $spellGain[] = ['name' => $parts[0], 'level_learned' => intval($parts[1]), 'cap' => intval($parts[2])];
        }

        // Armor levels
        $armorLevels = array_map('intval', explode(' ', $data[$idx++]));
        $armor = ['leather' => $armorLevels[0], 'studded' => $armorLevels[1], 'chain' => $armorLevels[2], 'light_plate' => $armorLevels[3], 'heavy_plate' => $armorLevels[4]];

        // Shield levels
        $shieldLevels = array_map('intval', explode(' ', $data[$idx++]));
        $shield = ['small' => $shieldLevels[0], 'medium' => $shieldLevels[1], 'large' => $shieldLevels[2], 'huge' => $shieldLevels[3]];

        // Caps
        $caps = array_map('floatval', explode(' ', $data[$idx++]));
        $capsData = ['haste' => $caps[0], 'armor' => $caps[1], 'resist' => $caps[2]];

        return [
            'class' => $classType,
            'character_creation' => [
                'starting_hp' => $hp,
                'starting_mana' => $mana,
                'starting_stats' => [
                    'str' => $str,
                    'tog' => $tog,
                    'agi' => $agi,
                    'foc' => $foc,
                    'int' => $int,
                    'cha' => $cha,
                ],
                'starting_skills' => $skills,
                'starting_spells' => $spells,
            ],
            'level_up' => [
                'hp_gain' => $hpGain,
                'hp_scale' => $hpScale,
                'damage_gain' => $dmgGain,
                'damage_scale' => $dmgScale,
                'mana_scale' => $manaScale,
                'skill_point_interval' => $skillInterval,
                'spell_point_interval' => $spellInterval,
            ],
            'skills' => $skillGain,
            'spells' => $spellGain,
            'armor_levels' => $armor,
            'shield_levels' => $shield,
            'caps' => $capsData,
        ];
    }
}
