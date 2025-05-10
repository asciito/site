<?php

use App\Blog\Models\Post;
use function Pest\Laravel\get;

it('can render', function () {
    get(
        route('home')
    )
        ->assertSuccessful()
        ->assertSeeLivewire('posts');
});

it('can render posts', function () {
    $posts = Post::factory(5)->published()->create();

    volt('posts')
        ->assertDontSeeText("There' no post available", false)
        ->assertSeeText($posts->pluck('title')->toArray());
});

test('no posts available', function () {
    Post::factory(10)->create();

    volt('posts')
        ->assertSeeText("There' no post available", false);
});

it('can show posts dynamically', function () {
    $posts = Post::factory(15)->published()->create();

    $component = volt('posts');

    expect($component->posts)->toHaveCount(5);

    $component->call('loadMorePosts');

    expect($component->posts)->toHaveCount(10);

    $component->call('loadMorePosts');

    expect($component->posts)->toHaveCount(15);

    $component->assertSeeText($posts->pluck('title')->toArray());
});

it('can\'t show more posts', function () {
    $posts = Post::factory(10)->published()->create();

    $component = volt('posts');

    expect($component->posts)->toHaveCount(5);

    $component->call('loadMorePosts');

    expect($component->posts)->toHaveCount(10);

    $component->call('loadMorePosts');

    expect($component->posts)->toHaveCount(10);
});
