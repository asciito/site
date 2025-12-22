<?php

use App\Enums\Status;
use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Models\Post;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseCount;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Livewire\livewire;

it('can render', function () {
    livewire(CreatePost::class)
        ->assertSuccessful();
});

it('can create post', function () {
    livewire(CreatePost::class)
        ->fillForm([
            'title' => $title = fake()->sentence(),
            'content' => fake()->randomHtml(),
        ])
        ->assertFormSet([
            'slug' => $slug = Str::slug($title),
        ])
        ->call('create')
        ->assertHasNoErrors();

    assertDatabaseCount('posts', 1);
    assertDatabaseHas('posts', [
        'title' => $title,
        'slug' => $slug,
        'status' => Status::DRAFT,
    ]);
});

it('can be published', function () {
    livewire(CreatePost::class)
        ->fillForm([
            'title' => $title = fake()->sentence(),
            'content' => fake()->randomHtml(),
        ])
        ->assertFormSet([
            'slug' => Str::slug($title),
        ])
        ->callAction('publish')
        ->assertHasNoErrors()
        ->assertRedirectToRoute(EditPost::getRouteName(), ['record' => 1]);

    assertDatabaseHas('posts', [
        'title' => $title,
        'status' => Status::PUBLISHED,
    ]);
});

it('can add thumbnail', function () {
    Storage::fake();

    $image = UploadedFile::fake()->image('fake-image.jpeg', 1920, 1080);

    livewire(CreatePost::class)
        ->fillForm([
            'title' => fake()->sentence(),
            'content' => fake()->randomHtml(),
            'thumbnail' => $image,
        ])
        ->call('create')
        ->assertHasNoErrors();

    expect(Post::withDrafts()->first())
        ->getMedia()
        ->toHaveCount(1);
});

it('can\'t add over-dimensioned image', function () {
    $image = UploadedFile::fake()->image('fake-image.jpeg', 2640, 1485);

    livewire(CreatePost::class)
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

    assertDatabaseCount('posts', 0);
});

it('can add excerpt', function () {
    livewire(CreatePost::class)
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

    assertDatabaseCount('posts', 1);
    assertDatabaseHas('posts', [
        'title' => $title,
        'slug' => Str::slug($title),
        'excerpt' => $excerpt,
    ]);
});
