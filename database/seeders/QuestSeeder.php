<?php

namespace Database\Seeders;

use App\Enums\ClassType;
use App\Enums\Quests\QuestRewardType;
use App\Models\Mob;
use App\Models\Quest;
use App\ValueObjects\Quests\QuestObject;
use App\ValueObjects\Quests\QuestReward;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;

class QuestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/data/Quests.json');
        $now = now();

        foreach (JsonParser::parse($file) as $key => $rawItem) {

            $classStrings = explode(' ', $rawItem['RequiredClass']);
            $classes = [];
            foreach ($classStrings as $str) {
                $type = ClassType::tryFrom(\Str::lower(trim($str)));

                if ($type !== null) {
                    $classes[] = $type;
                }
            }

            // quest rewards
            $rewards = [];
            $rewardTypes = [];
            foreach ($rawItem['Rewards'] as $rawReward) {
                $reward = new QuestReward(
                    type: QuestRewardType::from(\Str::snake($rawReward['Type'])),
                    name: $rawReward['Name'],
                    amount: $rawReward['Quantity'],
                );

                if ($reward->type !== QuestRewardType::Item) {
                    $rewards[] = $reward;
                }

                $rewardTypes[] = $reward->type;
            }
            $mob = Mob::where('name', $rawItem['NpcQuestGiver'])->first();

            $quest = new QuestObject(
                name: $rawItem['Name'],
                external_id: $rawItem['Id'],
                level: $rawItem['Level'],
                objectives: $rawItem['Objectives'],
                steps: $rawItem['Steps'],
                mob_id: $mob->id ?? null,
                required_class: $classes ?? null,
                raw_rewards: $rewards,
                reward_types: collect($rewardTypes)->unique()->values()->toArray(),
            );

            $arrayData = collect($quest->toArray())
                ->transform(function (mixed $value) {
                    if (is_array($value)) {
                        return !empty($value) ? json_encode($value) : null;
                    }

                    return $value;
                })
                ->toArray();

            $arrayData['created_at'] = $now;
            $arrayData['updated_at'] = $now;
            $arrayData['slug'] = \Str::slug(implode('-', [$arrayData['name'], $arrayData['external_id']]));

            $toInsert[] = $arrayData;

            if (count($toInsert) > 500) {
                Quest::insert($toInsert);
                unset($toInsert);
                $now = now();
            }
        }

        if (!empty($toInsert)) {
            Quest::insert($toInsert);
        }

        // one more pass to set up required quests
        foreach (JsonParser::parse($file) as $key => $rawItem) {
            if (!empty($rawItem['RequiredQuest'])) {
                $quest = Quest::where('external_id', $rawItem['Id'])->first();

                $requiredQuest = Quest::where('name', $rawItem['RequiredQuest'])->first();
                if ($requiredQuest === null) {
                    continue;
                }

                $quest->update([
                    'previous_quest_id' => $requiredQuest->id,
                ]);
            }
        }
    }
}
