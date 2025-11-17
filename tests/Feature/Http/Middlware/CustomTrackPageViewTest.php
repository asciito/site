<?php

beforeEach(fn () => app()->detectEnvironment(fn () => 'production'));
afterEach(fn () => app()->detectEnvironment(fn () => 'testing'));

dataset('routes', fn () => [
    'home',
    'contact',
    'terms',
    'privacy',
]);

dataset('draft posts', fn () => \App\Blog\Models\Post::factory(10)->create());

it('track page visited by non-login user', function (string $route) {
    $fake = \Illuminate\Support\Facades\Http::fake();

    \Pest\Laravel\get(route($route))->assertOk();

    $fake->assertSentCount(1);
})->with('routes');

it('does not track page visited by login user', function (string $route) {
    $fake = \Illuminate\Support\Facades\Http::fake();

    /** @var \Illuminate\Database\Eloquent\Model&\Illuminate\Contracts\Auth\Authenticatable */
    $user = \App\Models\User::factory()->create()->first();

    \Pest\Laravel\actingAs($user)
        ->get(route($route))
        ->assertOk();

    $fake->assertSentCount(0);
})->with('routes');
