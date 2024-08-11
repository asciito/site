<?php

beforeEach(fn () => $this->app->detectEnvironment(fn () => 'production'));
afterEach(fn () => $this->app->detectEnvironment(fn () => 'testing'));

dataset('routes', fn () => [
    'home',
    'contact',
    'terms',
    'privacy',
]);

dataset('draft posts', fn () => \App\Models\Post::factory(10)->create());

it('track page visited by non-login user', function (string $route) {
    $fake = \Illuminate\Support\Facades\Http::fake();

    \Pest\Laravel\get(route($route))->assertOk();

    $fake->assertSentCount(1);
})->with('routes');

it('does not track page visited by login user', function (string $route) {
    $fake = \Illuminate\Support\Facades\Http::fake();

    \Pest\Laravel\actingAs(\App\Models\User::factory()->create())
        ->get(route($route))
        ->assertOk();

    $fake->assertSentCount(0);
})->with('routes');
