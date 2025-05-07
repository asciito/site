<?php

use App\Filament\Resources\PostResource\Pages;

it('can render', function () {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->assertSuccessful();
});

it('can create post', function () {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm([
            'title' => $title = fake()->sentence(),
            'content' => fake()->randomHtml(),
        ])
        ->assertFormSet([
            'slug' => $slug = \Illuminate\Support\Str::slug($title),
        ])
        ->call('create')
        ->assertHasNoErrors();

    \Pest\Laravel\assertDatabaseCount('posts', 1);
    \Pest\Laravel\assertDatabaseHas('posts', [
        'title' => $title,
        'slug' => $slug,
        'status' => \App\Site\Enums\Status::DRAFT,
    ]);
});

it('can be published', function () {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm([
            'title' => $title = fake()->sentence(),
            'content' => fake()->randomHtml(),
        ])
        ->assertFormSet([
            'slug' => \Illuminate\Support\Str::slug($title),
        ])
        ->callAction('publish')
        ->assertHasNoErrors()
        ->assertRedirectToRoute(Pages\EditPost::getRouteName(), ['record' => 1]);

    \Pest\Laravel\assertDatabaseHas('posts', [
        'title' => $title,
        'status' => \App\Site\Enums\Status::PUBLISHED,
    ]);
});

it('can add thumbnail', function () {
    Storage::fake();

    $image = \Illuminate\Http\UploadedFile::fake()->image('fake-image.jpeg', 1920, 1080);

    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm([
            'title' => fake()->sentence(),
            'content' => fake()->randomHtml(),
            'thumbnail' => $image,
        ])
        ->call('create')
        ->assertHasNoErrors();

    expect(\App\Models\Post::withDrafts()->first())
        ->getMedia()
        ->toHaveCount(1);
});

it('can\'t add over-dimensioned image', function () {
    $image = \Illuminate\Http\UploadedFile::fake()->image('fake-image.jpeg', 2640, 1485);

    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm([
            'title' => fake()->sentence(),
            'content' => fake()->randomHtml(),
            'thumbnail' => $image,
        ])
        ->call('create')
        ->assertHasFormErrors([
            'thumbnail' => 'The thumbnail dimensions are not valid',
        ])
        ->errors();

    \Pest\Laravel\assertDatabaseCount('posts', 0);
});

it('can add excerpt', function () {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm([
            'title' => $title = fake()->sentence(),
            'content' => fake()->randomHtml(),
            'excerpt' => $excerpt = fake()->text(),
        ])
        ->assertFormSet([
            'excerpt' => $excerpt,
        ])
        ->call('create')
        ->assertHasNoErrors();

    \Pest\Laravel\assertDatabaseCount('posts', 1);
    \Pest\Laravel\assertDatabaseHas('posts', [
        'title' => $title,
        'slug' => \Illuminate\Support\Str::slug($title),
        'excerpt' => $excerpt,
    ]);
});
