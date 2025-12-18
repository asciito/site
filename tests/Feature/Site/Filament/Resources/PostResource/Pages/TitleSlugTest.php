<?php

use App\Blog\Filament\Resources\Posts\Pages\CreatePost;
use App\Blog\Filament\Resources\Posts\Pages\EditPost;
use App\Blog\Models\Post;
use Illuminate\Support\Str;

use function Pest\Livewire\livewire;

beforeEach(fn () => $this->markTestSkipped('Skipped for performance reasons'));

dataset('titles', function () {
    foreach (range(1, 10) as $_) {
        $title = fake()->unique()->sentence();

        $data[] = [$title];
    }

    return $data;
});

it('can render', function (string $title) {
    livewire(CreatePost::class)
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('slug')
        ->fillForm([
            'title' => $title,
            'slug' => $title,
        ])
        ->assertFormSet([
            'title' => $title,
            'slug' => Str::slug($title),
        ])
        ->assertSuccessful();
})->with('titles');

it('can sync title and slug', function (string $title) {
    livewire(CreatePost::class)
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => Str::slug($title)])
        ->fillForm(['slug' => $slug = Str::slug(fake()->sentence)])
        ->assertFormSet(['slug' => $slug])
        ->assertSuccessful();
})->with('titles');

it('can edit slug and update title without sync slug with title', function (string $title) {
    livewire(CreatePost::class)
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => Str::slug($title)])
        ->fillForm(['slug' => $slug = Str::slug(fake()->sentence())])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' => $title = fake()->sentence()])
        ->assertFormSet(['slug' => $slug])
        ->assertSuccessful();
})->with('titles');

it('can sync title and slug again after math edited slug with title', function (string $title) {
    livewire(CreatePost::class)
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => Str::slug($title)])
        ->fillForm(['slug' => $slug = Str::slug(fake()->sentence)])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' => fake()->sentence])
        ->assertFormSet(['slug' => $slug])
        ->assertSuccessful();

    $post = Post::factory()->create([
        'title' => $title,
        'slug' => $slug = Str::slug(fake()->unique()->sentence),
    ]);

    livewire(EditPost::class, ['record' => $post->id])
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' => Str::of($slug)->replace('-', ' ')->title()])
        ->fillForm(['title' => $title = fake()->unique()->sentence()])
        ->assertFormSet(['slug' => Str::slug($title)]);
})->with('titles');

it('must prevent edit slug if post is published', function (string $title) {
    $post = Post::factory()
        ->published()
        ->dontSyncSlug()
        ->create();

    livewire(EditPost::class, ['record' => $post->id])
        ->fillForm(['title' => $title])
        ->assertFormSet(function (array $state) use ($title) {
            expect($state['slug'])->not->toBe(Str::slug($title));
        })
        ->fillForm(['slug' => $slug = Str::slug(fake()->sentence)])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' => $title = fake()->sentence()])
        ->assertFormSet(function (array $state) use ($title) {
            expect($state['slug'])->not->toBe(Str::slug($title));
        })
        ->assertSuccessful();
})->with('titles');
