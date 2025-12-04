<?php

use App\Models\Quest;
use Database\Seeders\QuestSeeder;

test('Quests can be imported', function () {
    $this->seed(QuestSeeder::class);
    $questsToBeImported = Quest::all()->random(10);

    $response = $this->postJson('/import-quest', [
        'names' => $questsToBeImported->pluck('name')->toArray(),
    ]);

    $responseObject = collect(json_decode($response->getContent(), true));

    expect($responseObject->filter(fn ($quest) => $questsToBeImported->contains('id', $quest)
    ))->toHaveCount($questsToBeImported->count());
});
