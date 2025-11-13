<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Mob;
use App\Models\Pivots\ItemMob;
use Illuminate\Database\Seeder;

class MobToItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::pluck('id', 'slug');
        $mobs = Mob::query()->whereJsonLength('drops', '>', 0)->get();

        $pivotsToInsert = [];
        foreach ($mobs as $mob) {
            foreach ($mob->drops as $drop) {
                if ($itemId = $items->get(\Str::slug($drop))) {
                    $pivotsToInsert[] = [
                        'mob_id' => $mob->id,
                        'item_id' => $itemId,
                    ];
                }
            }
        }

        if (! empty($pivotsToInsert)) {
            ItemMob::insert($pivotsToInsert);
        }
    }
}
