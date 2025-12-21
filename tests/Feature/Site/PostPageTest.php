<?php

use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

it('can render', function () {
    $post = Post::factory()->published()->create();

    get(route('post', $post))->assertOk();
});

test('author can see draft post', function () {
    $post = Post::factory()->create();

    actingAs(User::factory()->create())
        ->get(route('post', ['post' => $post]))->assertOk();
});

test('author can see archived post', function () {
    $post = Post::factory()->archived()->create();

    expect($post->isArchived())->toBeTrue();

    actingAs(User::factory()->create())
        ->get(route('post', $post))
        ->assertOk();
})->todo();

test('user can\'t see draft post', function () {
    $post = Post::factory()->create();

    get(route('post', $post))->assertNotFound();
});

test('user can\'t see archived post', function () {
    $post = Post::factory()->trashed()->create();

    get(route('post', $post))->assertNotFound();
});
