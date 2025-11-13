<?php

namespace Database\Seeders;

use App\Enums\Mobs\MobTier;
use App\Enums\Mobs\MobType;
use App\Models\Mob;
use App\ValueObjects\Mobs\MobDeprecatedData;
use App\ValueObjects\Mobs\MobObject;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;

class MobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/data/Mobs.json');
        $now = now();

        foreach (JsonParser::parse($file) as $key => $rawItem) {
            $mobType = MobType::toEnum($rawItem['IsBoss'] ? 'boss' : $rawItem['Type']);

            $mob = new MobObject(
                external_id: $rawItem['Id'],
                name: $rawItem['Name'],
                level: $rawItem['Level'],
                type: $mobType,
                tier: MobTier::toEnum($rawItem['Tier']),
                location: explode(', ', $rawItem['Location']),
                deprecated_data: new MobDeprecatedData(
                    health: falseyToNull($rawItem['Health']),
                    experience: falseyToNull($rawItem['Experience']),
                    damage: falseyToNull($rawItem['Damage']),
                ),
                drops: collect($rawItem['DroppedItems'])->map(fn (string $item) => trim($item))->toArray(),
            );

            $arrayData = collect($mob->toArray())
                ->transform(function (mixed $value) {
                    if (is_array($value)) {
                        $filtered = array_filter($value);

                        return ! empty($filtered) ? json_encode($filtered) : null;
                    }

                    return $value;
                })
                ->toArray();
            $arrayData['created_at'] = $now;
            $arrayData['updated_at'] = $now;
            $arrayData['slug'] = \Str::slug(implode('-', [$arrayData['name'], $arrayData['external_id']]));

            $toInsert[] = $arrayData;

            if (count($toInsert) > 500) {
                Mob::insert($toInsert);
                unset($toInsert);
                $now = now();
            }
        }

        if (! empty($toInsert)) {
            Mob::insert($toInsert);
        }
    }
}
