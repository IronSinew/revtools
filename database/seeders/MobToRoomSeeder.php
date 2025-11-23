<?php

namespace Database\Seeders;

use App\Models\Mob;
use App\Models\Pivots\MobRoom;
use App\Models\Room;
use Illuminate\Database\Seeder;

class MobToRoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mobs = Mob::pluck('id', 'name');
        $rooms = Room::query()->whereJsonLength('npcs', '>', 0)->get();

        $pivotsToInsert = [];
        foreach ($rooms as $room) {
            foreach ($room->npcs as $npc) {
                if ($mobId = $mobs->get($npc)) {
                    $pivotsToInsert[] = [
                        'room_id' => $room->id,
                        'mob_id' => $mobId,
                    ];
                }
            }
        }

        if (! empty($pivotsToInsert)) {
            MobRoom::insert($pivotsToInsert);
        }
    }
}
