<?php

namespace App;

use App\Enums\ClassType;

class ReqFinder
{
    private array $dataByClass;

    public function __construct()
    {
        $file = storage_path('app/data/Classes.json');
        $this->dataByClass = json_decode(file_get_contents($file), true);
    }

    public function match(string $skillOrSpellName, int $levelRequired = 1): array
    {
        $skillOrSpellName = strtolower($skillOrSpellName);

        $classesFound = [];

        foreach ($this->dataByClass as $classData) {
            foreach ($classData['skills'] as $skill) {
                if (strtolower($skill['name']) == $skillOrSpellName && $skill['cap'] >= $levelRequired) {
                    $classType = ClassType::tryFrom($classData['class']);
                    if ($classType) {
                        $classesFound[] = $classType;
                    }
                }
            }

            foreach ($classData['spells'] as $spell) {
                if (strtolower($spell['name']) == $skillOrSpellName && $spell['cap'] >= $levelRequired) {
                    $classType = ClassType::tryFrom($classData['class']);
                    if ($classType) {
                        $classesFound[] = $classType;
                    }
                }
            }
        }

        return $classesFound;
    }
}
