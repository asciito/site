<?php

use App\Models\Post;

use function Pest\Laravel\get;

it('can render home page', function () {
    get(
        route('home')
    )->assertSuccessful();
});

test('home can render posts', function () {
    Post::factory(10)
        ->create()
        ->each
        ->publish();

    get(
        route('home')
    )
        ->assertSeeLivewire('posts')
        ->assertDontSeeText("There' no post available\n", false);
});

test('home has no posts to show', function () {
    get(
        route('home')
    )
        ->assertSeeLivewire('posts')
        ->assertSeeText("There' no post available\n", false);
});

it('can render contact page', function () {
    get(
        route('contact')
    )->assertSuccessful();
});

test('contact has form', function () {
    get(
        route('contact')
    )
        ->assertSeeLivewire('contact-form');
});

it('can render terms and use page', function () {
    get(
        route('terms')
    )->assertSuccessful();
});

it('can render privacy policy page', function () {
    get(
        route('privacy')
    )->assertSuccessful();
});
