<?php

use App\Enums\ClassType;
use App\Models\Item;
use App\Models\Mob;
use App\Models\Quest;
use App\Models\Region;
use App\Models\Room;
use App\Models\User;
use App\ReqFinder;
use Database\Seeders\ItemSeeder;
use Database\Seeders\MobSeeder;
use Database\Seeders\MobToItemSeeder;
use Database\Seeders\QuestSeeder;
use Database\Seeders\RegionSeeder;
use Database\Seeders\RoomSeeder;

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

test('that regions are parsable', function () {
    $this->seed(MobSeeder::class)->seed(ItemSeeder::class)->seed(RegionSeeder::class)->seed(RoomSeeder::class);

    $region = Room::whereNotNull('region_id')->first()->region;

    expect($region)->toBeInstanceOf(Region::class)->and($region->rooms()->first())->toBeInstanceOf(Room::class);
});

test('that spell or skill requirements return classes that are eligible', function () {
    // Dodge 5 should match Ranger, Redeemer, Priest
    // Warrior has dodge, but it caps at 3, so Dodge 5 isn't possible for it to wear
    $matchClasses = (new ReqFinder)->match('Dodge', 5);

    expect($matchClasses)
        ->toContain(ClassType::Ranger, ClassType::Redeemer, ClassType::Priest)
        ->not->toContain(ClassType::Warrior);
});
