<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\Quests\QuestRewardType;
use App\Models\Item;
use App\Models\Quest;
use App\ValueObjects\Quests\QuestObject;
use App\ValueObjects\Quests\QuestReward;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;

class ItemQuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/data/Quests.json');
        $now = now();

        foreach (JsonParser::parse($file) as $key => $rawItem) {
            $quest = Quest::where('external_id', $rawItem['Id'])->first();

            // raw quest rewards
            $rewards = [];
            foreach ($rawItem['Rewards'] as $rawReward) {
                $reward = new QuestReward(
                    type: QuestRewardType::from(\Str::snake($rawReward['Type'])),
                    name: $rawReward['Name'],
                    amount: $rawReward['Quantity'],
                );

                if ($reward->type === QuestRewardType::Item) {
                    $item = Item::where('name', $reward->name)->first();

                    if ($item !== null) {
                        $quest->items()->attach($item->id);

                        $rewards[] = $reward;
                    }
                }
            }
        }
    }
}
