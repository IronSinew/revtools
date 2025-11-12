<?php

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Jetstream\Features;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('api tokens can be deleted', function () {
    if (! Features::hasApiFeatures()) {
        $this->markTestSkipped('API support is not enabled.');
    }

    $this->actingAs($user = User::factory()->withPersonalTeam()->create());

    $token = $user->tokens()->create([
        'name' => 'Test Token',
        'token' => Str::random(40),
        'abilities' => ['create', 'read'],
    ]);

    $response = $this->delete('/user/api-tokens/'.$token->id);

    expect($user->fresh()->tokens)->toHaveCount(0);
});
