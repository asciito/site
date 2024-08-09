<?php

use App\Filament\Resources\PostResource\Pages;

it('can render', function () {
    \Pest\Livewire\livewire(Pages\ListPosts::class)
        ->assertSuccessful();
});

it('can list posts', function () {
    \App\Models\Post::factory(10)->create();

    \Pest\Livewire\livewire(Pages\ListPosts::class)
        ->assertSuccessful()
        ->assertCountTableRecords(10);
});

it('can see archive posts', function () {
    $archived = \App\Models\Post::factory(5)->archived()->create();
    $published = \App\Models\Post::factory(10)->published()->create();

    \Pest\Livewire\livewire(Pages\ListPosts::class)
        ->assertSuccessful()
        ->filterTable('trashed', false)
        ->assertCountTableRecords(5)
        ->assertCanNotSeeTableRecords($published)
        ->assertCanSeeTableRecords($archived);
});

it('can list draft posts', function () {
    $drafts = \App\Models\Post::factory(5)->create();
    $published = \App\Models\Post::factory(10)->published()->create();

    \Pest\Livewire\livewire(Pages\ListPosts::class)
        ->assertSuccessful()
        ->filterTable('status', false)
        ->assertCountTableRecords(5)
        ->assertCanNotSeeTableRecords($published)
        ->assertCanSeeTableRecords($drafts);
});

it('can list published posts', function () {
    $drafts = \App\Models\Post::factory(5)->create();
    $archived = \App\Models\Post::factory(5)->archived()->create();
    $published = \App\Models\Post::factory(10)->published()->create();

    \Pest\Livewire\livewire(Pages\ListPosts::class)
        ->assertSuccessful()
        ->filterTable('status', true)
        ->assertCountTableRecords(10)
        ->assertCanNotSeeTableRecords($drafts->merge($archived))
        ->assertCanSeeTableRecords($published);
});
