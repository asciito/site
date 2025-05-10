<?php

use App\Filament\Resources\PostResource\Pages;

it('can render', function () {
    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\PostResource\Pages\ListPosts::class)
        ->assertSuccessful();
});

it('can list posts', function () {
    \App\Blog\Models\Post::factory(10)->create();

    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\PostResource\Pages\ListPosts::class)
        ->assertSuccessful()
        ->assertCountTableRecords(10);
});

it('can see archive posts', function () {
    $archived = \App\Blog\Models\Post::factory(5)->archived()->create();
    $published = \App\Blog\Models\Post::factory(10)->published()->create();

    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\PostResource\Pages\ListPosts::class)
        ->assertSuccessful()
        ->filterTable('trashed', false)
        ->assertCountTableRecords(5)
        ->assertCanNotSeeTableRecords($published)
        ->assertCanSeeTableRecords($archived);
});

it('can list draft posts', function () {
    $drafts = \App\Blog\Models\Post::factory(5)->create();
    $published = \App\Blog\Models\Post::factory(10)->published()->create();

    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\PostResource\Pages\ListPosts::class)
        ->assertSuccessful()
        ->filterTable('status', false)
        ->assertCountTableRecords(5)
        ->assertCanNotSeeTableRecords($published)
        ->assertCanSeeTableRecords($drafts);
});

it('can list published posts', function () {
    $drafts = \App\Blog\Models\Post::factory(5)->create();
    $archived = \App\Blog\Models\Post::factory(5)->archived()->create();
    $published = \App\Blog\Models\Post::factory(10)->published()->create();

    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\PostResource\Pages\ListPosts::class)
        ->assertSuccessful()
        ->filterTable('status', true)
        ->assertCountTableRecords(10)
        ->assertCanNotSeeTableRecords($drafts->merge($archived))
        ->assertCanSeeTableRecords($published);
});
