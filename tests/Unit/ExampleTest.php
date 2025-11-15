<?php

use App\Models\Item;
use App\Models\Mob;
use App\Models\Quest;
use App\Models\User;
use Database\Seeders\ItemSeeder;
use Database\Seeders\MobSeeder;
use Database\Seeders\MobToItemSeeder;
use Database\Seeders\QuestSeeder;

test('that a user is creatable and exists', function () {
    $userFactory = User::factory()->create();

    $user = collect(\DB::select('select * from users where id = ?', [$userFactory->id]))->first();
    expect($user->id === $userFactory->id)->toBeTrue();
});

test('that items are parsable', function () {
    $this->seed(ItemSeeder::class);

    $item = Item::first();

    expect($item)->toBeInstanceOf(Item::class);
});

test('that mobs are parsable', function () {
    $this->seed(MobSeeder::class);

    $mob = Mob::first();

    expect($mob)->toBeInstanceOf(Mob::class);
});

test('that items to mobs relationships are parsable', function () {
    $this->seed(ItemSeeder::class);
    $this->seed(MobSeeder::class);
    $this->seed(MobToItemSeeder::class);

    $mobsWithItemRelationships = Mob::whereHas('items')->count();

    expect($mobsWithItemRelationships)->toBeGreaterThan(0);
});

test('that quests are parsable', function () {
    $this->seed(MobSeeder::class)->seed(QuestSeeder::class);

    $quest = Quest::whereNotNull('mob_id')->first();

    expect($quest)->toBeInstanceOf(Quest::class)->and($quest->mob)->toBeInstanceOf(Mob::class);
});
