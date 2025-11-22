<?php

namespace Database\Seeders;

use App\Enums\Regions\RoomColor;
use App\Models\Region;
use App\ValueObjects\Regions\RegionObject;
use App\ValueObjects\Regions\RegionPosition;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/data/WorldData.json');
        $now = now();

        foreach (JsonParser::parse($file)->lazy() as $key => $value) {
            if ($key !== 'Zones') {
                continue;
            }

            foreach ($value as $zoneKey => $rawZone) {
                $rawItem = iterator_to_array($rawZone);
                if (empty($rawItem['ZoneName'])) {
                    return;
                }

                $region = new RegionObject(
                    name: $rawItem['ZoneName'],
                );

                $arrayData = collect($region->toArray())
                    ->transform(function (mixed $value) {
                        if (is_array($value)) {
                            return ! empty($value) ? json_encode($value) : null;
                        }

                        return $value;
                    })
                    ->toArray();

                $arrayData['created_at'] = $now;
                $arrayData['updated_at'] = $now;
                $arrayData['slug'] = \Str::slug($arrayData['name']);

                $toInsert[] = $arrayData;

                if (count($toInsert) > 500) {
                    Region::insert($toInsert);
                    unset($toInsert);
                    $now = now();
                }
            }

            if (! empty($toInsert)) {
                Region::insert($toInsert);
            }
        }

        $file = storage_path('app/data/RegionMapData.json');
        foreach (JsonParser::parse($file) as $key => $value) {
            Region::where('name', $key)->first()
                ->update([
                    'coordinates' => new RegionPosition($value['X'], $value['Y'], $value['Z']),
                    'color' => RoomColor::from($value['Color'])->toColor(),
                ]);
        }
    }
}
