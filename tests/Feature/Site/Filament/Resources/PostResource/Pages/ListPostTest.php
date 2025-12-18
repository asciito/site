<?php

use App\Blog\Filament\Resources\Posts\Pages\ListPosts;
use App\Blog\Models\Post;

use function Pest\Livewire\livewire;

it('can render', function () {
    livewire(ListPosts::class)
        ->assertSuccessful();
});

it('can list posts', function () {
    Post::factory(10)->create();

    livewire(ListPosts::class)
        ->assertSuccessful()
        ->assertCountTableRecords(10);
});

it('can see archive posts', function () {
    $archived = Post::factory(5)->archived()->create();
    $published = Post::factory(10)->published()->create();

    livewire(ListPosts::class)
        ->assertSuccessful()
        ->filterTable('trashed', false)
        ->assertCountTableRecords(5)
        ->assertCanNotSeeTableRecords($published)
        ->assertCanSeeTableRecords($archived);
});

it('can list draft posts', function () {
    $drafts = Post::factory(5)->create();
    $published = Post::factory(10)->published()->create();

    livewire(ListPosts::class)
        ->assertSuccessful()
        ->filterTable('status', false)
        ->assertCountTableRecords(5)
        ->assertCanNotSeeTableRecords($published)
        ->assertCanSeeTableRecords($drafts);
});

it('can list published posts', function () {
    $drafts = Post::factory(5)->create();
    $archived = Post::factory(5)->archived()->create();
    $published = Post::factory(10)->published()->create();

    livewire(ListPosts::class)
        ->assertSuccessful()
        ->filterTable('status', true)
        ->assertCountTableRecords(10)
        ->assertCanNotSeeTableRecords($drafts->merge($archived))
        ->assertCanSeeTableRecords($published);
});
