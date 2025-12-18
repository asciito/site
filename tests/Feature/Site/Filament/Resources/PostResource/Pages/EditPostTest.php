<?php

use App\Blog\Filament\Resources\Posts\Pages\EditPost;
use App\Blog\Models\Post;
use Illuminate\Http\UploadedFile;

use function Pest\Livewire\livewire;

it('can render', function () {
    $post = Post::factory()->create();

    livewire(EditPost::class, ['record' => $post->id])
        ->assertSuccessful();
});

it('can fill edit form', function () {
    $post = Post::factory()->create();

    livewire(EditPost::class, ['record' => $post->id])
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
        ->not->toBe(Post::first());
});

it('can add missing thumbnail', function () {
    $storage = Storage::fake();

    $newImage = UploadedFile::fake()->image('fake-image.jpeg', 1920, 1080);

    $post = Post::factory()->create();

    livewire(EditPost::class, ['record' => $post->id])
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
