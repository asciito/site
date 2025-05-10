<?php

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can render', function () {
    $post = \App\Blog\Models\Post::factory()->published()->create();

    get(route('post', $post))->assertOk();
});

test('author can see draft post', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    actingAs(\App\Models\User::factory()->create())
        ->get(route('post', ['post' => $post]))->assertOk();
});

test('author can see archived post', function () {
    $post = \App\Blog\Models\Post::factory()->archived()->create();

    expect($post->isArchived())->toBeTrue();

    actingAs(\App\Models\User::factory()->create())
        ->get(route('post', $post))
        ->assertOk();
})->todo();

test('user can\'t see draft post', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    get(route('post', $post))->assertNotFound();
});

test('user can\'t see archived post', function () {
    $post = \App\Blog\Models\Post::factory()->trashed()->create();

    get(route('post', $post))->assertNotFound();
});
