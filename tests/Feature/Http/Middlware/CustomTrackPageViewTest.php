<?php

use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Http;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(fn () => $this->app->detectEnvironment(fn () => 'production'));
afterEach(fn () => $this->app->detectEnvironment(fn () => 'testing'));

dataset('routes', fn () => [
    'home',
    'contact',
    'terms',
    'privacy',
]);

dataset('draft posts', fn () => Post::factory(10)->create());

it('track page visited by non-login user', function (string $route) {
    $fake = Http::fake();

    get(route($route))->assertOk();

    $fake->assertSentCount(1);
})->with('routes');

it('does not track page visited by login user', function (string $route) {
    $fake = Http::fake();

    actingAs(User::factory()->create())
        ->get(route($route))
        ->assertOk();

    $fake->assertSentCount(0);
})->with('routes');
