<?php

namespace Database\Seeders;

use App\Models\Mob;
use App\Models\Pivots\ItemRoom;
use App\Models\Pivots\MobRoom;
use App\Models\Room;
use Illuminate\Database\Seeder;

class ItemToRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mobs = Mob::with('items')->get()->pluck('items', 'name');
        $rooms = Room::query()->whereJsonLength('npcs', '>', 0)->get();

        $pivotsToInsert = [];
        foreach ($rooms as $room) {
            foreach ($room->npcs as $npc) {
                if ($items = $mobs->get($npc)) {
                    foreach ($items as $item) {
                        $pivotsToInsert[] = [
                            'room_id' => $room->id,
                            'item_id' => $item->id,
                        ];
                    }
                }
            }
        }

        if (! empty($pivotsToInsert)) {
            ItemRoom::insert($pivotsToInsert);
        }
    }
}
