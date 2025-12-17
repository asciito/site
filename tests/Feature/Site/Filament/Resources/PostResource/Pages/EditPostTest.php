<?php

it('can render', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\Posts\Pages\EditPost::class, ['record' => $post->id])
        ->assertSuccessful();
});

it('can fill edit form', function () {
    $post = \App\Blog\Models\Post::factory()->create();

    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\Posts\Pages\EditPost::class, ['record' => $post->id])
        ->fillForm([
            'title' => $title = 'This is a new title',
            'slug' => $slug = 'just-a-new-slug',
            'content' => $content = fake()->randomHtml(),
        ])
        ->assertFormSet([
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSuccessful();

    expect($post)
        ->not->toBe(\App\Blog\Models\Post::first());
});

it('can add missing thumbnail', function () {
    $storage = Storage::fake();

    $newImage = \Illuminate\Http\UploadedFile::fake()->image('fake-image.jpeg', 1920, 1080);

    $post = \App\Blog\Models\Post::factory()->create();

    \Pest\Livewire\livewire(\App\Blog\Filament\Resources\Posts\Pages\EditPost::class, ['record' => $post->id])
        ->fillForm([
            'thumbnail' => $newImage,
        ])
        ->call('save')
        ->assertHasNoErrors()
        ->assertSuccessful();

    expect($post)
        ->getFirstMedia()
        ->not->toBeNull()
        ->and($post->getFirstMedia()->exists())
        ->toBeTrue();
});
