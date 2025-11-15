<?php

namespace Database\Seeders;

use App\Models\Quest;
use Cerbero\JsonParser\JsonParser;
use Illuminate\Database\Seeder;

class QuestChainSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $file = storage_path('app/data/Quests.json');

        foreach (JsonParser::parse($file) as $key => $rawItem) {
            $quest = Quest::where('external_id', $rawItem['Id'])->first();

            $chain = collect([$quest]);
            $currentQuest = $quest;

            while ($currentQuest->previous_quest_id) {
                $currentQuest = Quest::find($currentQuest->previous_quest_id);

                if ($currentQuest) {
                    $chain->prepend($currentQuest);
                } else {
                    break;
                }
            }
            $currentQuest = $quest;

            while ($nextQuest = Quest::where('previous_quest_id', $currentQuest->id)->first()) {
                $chain->push($nextQuest);
                $currentQuest = $nextQuest;
            }

            if ($chain->count() > 1) {
                $quest->update(['quest_chain' => $chain->pluck('id')->toArray()]);
            }
        }
    }
}
