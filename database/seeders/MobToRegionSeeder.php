<?php

namespace Database\Seeders;

use App\Models\Mob;
use App\Models\Pivots\MobRegion;
use Illuminate\Database\Seeder;

class MobToRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mobs = Mob::with('rooms.region')->get();

        $pivotsToInsert = [];
        foreach ($mobs as $mob) {
            $regions = $mob->rooms->map(fn ($room) => $room->region)->unique();

            $regions->each(function ($region) use ($mob, &$pivotsToInsert) {
                $pivotsToInsert[] = [
                    'region_id' => $region->id,
                    'mob_id' => $mob->id,
                ];
            });
        }

        if (! empty($pivotsToInsert)) {
            MobRegion::insert($pivotsToInsert);
        }
    }
}
