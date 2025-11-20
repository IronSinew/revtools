<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Mob;
use App\Models\Quest;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Laravel\Scout\Searchable;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            MobSeeder::class,
            ItemSeeder::class,
            QuestSeeder::class,
            ItemQuestSeeder::class,
            MobToItemSeeder::class,
            QuestChainSeeder::class,
            RegionSeeder::class,
            RoomSeeder::class,
            MobToRoomSeeder::class,
        ]);

        // Rebuild scout models
        $rebuildModels = [Mob::class, Item::class, Quest::class];

        // Just doing the one model removal is fine since they all share the same index
        Item::removeAllFromSearch();

        foreach ($rebuildModels as $model) {
            /** @var Searchable $instance */
            $instance = new $model;
            $instance::makeAllSearchable();
        }
    }
}
