<?php

use App\Models\Item;
use App\Models\User;
use Database\Seeders\ItemSeeder;

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
