<?php

use Database\Seeders\MobSeeder;
use Inertia\Testing\AssertableInertia as Assert;

// Sometimes we can get partial steps on the slider, this ensures levels are cast to an int in that scenario
test('Item Search by Level with non-int steps functions', function () {
    $this->seed(MobSeeder::class);

    $response = $this->get(route('item.index', [
        'filters' => [
            'effective_required_level' => [
                'value' => [64, 84.25],
            ],
        ],
    ]));

    $response
        ->assertInertia(fn (Assert $page) => $page
            ->missing('table') // Deferred prop not in initial response
            ->loadDeferredProps(fn (Assert $reload) => $reload
                ->has('table')
            )
        );

    $response->assertStatus(200);
});
