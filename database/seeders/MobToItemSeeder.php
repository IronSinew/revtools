<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Mob;
use App\Models\Pivots\ItemMob;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;

class MobToItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = Item::pluck('id', 'slug');
        $itemsByExternalId = Item::pluck('id', 'external_id');
        $mobs = Mob::query()->get()->keyBy('name');

        $pivotsToInsert = [];
        foreach ($mobs as $mob) {
            foreach ($mob->drops ?? [] as $drop) {
                if ($itemId = $items->get(\Str::slug($drop))) {
                    $pivotsToInsert["{$mob->id}-{$itemId}"] = [
                        'mob_id' => $mob->id,
                        'item_id' => $itemId,
                    ];
                }
            }
        }

        $file = storage_path('app/data/Items.json');
        foreach (JsonParser::parse($file) as $key => $rawItem) {
            $removeSuper = ['¹', '²', '³', '⁴', '⁵', '⁶', '⁷', '⁸', '⁹', '⁰'];
            $mobCollection = collect(explode(',', \Str::replace($removeSuper, '', $rawItem['DroppedBy'])))
                ->map(function (string $item) {
                    return trim($item);
                })
                ->unique();

            foreach ($mobCollection as $mob) {
                $itemId = $itemsByExternalId->get($rawItem['Id']);
                if ($mobModel = $mobs->get($mob)) {
                    $pivotsToInsert["{$mobModel->id}-{$itemId}"] = [
                        'mob_id' => $mobModel->id,
                        'item_id' => $itemId,
                    ];
                }
            }
        }

        if (! empty($pivotsToInsert)) {
            ItemMob::insert(array_values($pivotsToInsert));
        }
    }
}
