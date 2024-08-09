<?php

use App\Filament\Resources\PostResource\Pages;

dataset('titles', function () {
    foreach (range(1, 10) as $_) {
        $title = fake()->unique()->sentence();

        $data[] = [$title];
    }

    return $data;
});

it('can render', function (string $title) {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->assertFormFieldExists('title')
        ->assertFormFieldExists('slug')
        ->fillForm([
            'title' => $title,
            'slug' => $title,
        ])
        ->assertFormSet([
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
        ])
        ->assertSuccessful();
})->with('titles');

it('can sync title and slug', function (string $title) {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => \Illuminate\Support\Str::slug($title)])
        ->fillForm(['slug' => $slug = \Illuminate\Support\Str::slug(fake()->sentence)])
        ->assertFormSet(['slug' => $slug])
        ->assertSuccessful();
})->with('titles');

it('can edit slug and update title without sync slug with title', function (string $title) {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => \Illuminate\Support\Str::slug($title)])
        ->fillForm(['slug' => $slug = \Illuminate\Support\Str::slug(fake()->sentence())])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' => $title = fake()->sentence()])
        ->assertFormSet(['slug' => $slug])
        ->assertSuccessful();
})->with('titles');

it('can sync title and slug again after math edited slug with title', function (string $title) {
    \Pest\Livewire\livewire(Pages\CreatePost::class)
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => \Illuminate\Support\Str::slug($title)])
        ->fillForm(['slug' => $slug = \Illuminate\Support\Str::slug(fake()->sentence)])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' =>fake()->sentence])
        ->assertFormSet(['slug' => $slug])
        ->assertSuccessful();

    $post = \App\Models\Post::factory()->create([
        'title' => $title,
        'slug' => $slug = \Illuminate\Support\Str::slug(fake()->unique()->sentence),
    ]);

    \Pest\Livewire\livewire(Pages\EditPost::class, ['record' => $post->id])
        ->fillForm(['title' => $title])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' => \Illuminate\Support\Str::of($slug)->replace('-', ' ')->title()])
        ->fillForm(['title' => $title = fake()->unique()->sentence()])
        ->assertFormSet(['slug' => \Illuminate\Support\Str::slug($title)]);
})->with('titles');

it('must prevent edit slug if post is published', function (string $title) {
    $post = \App\Models\Post::factory()
        ->published()
        ->dontSyncSlug()
        ->create();

    \Pest\Livewire\livewire(Pages\EditPost::class, ['record' => $post->id])
        ->fillForm(['title' => $title])
        ->assertFormSet(function (array $state) use ($title) {
            expect($state['slug'])->not->toBe(\Illuminate\Support\Str::slug($title));
        })
        ->fillForm(['slug' => $slug = \Illuminate\Support\Str::slug(fake()->sentence)])
        ->assertFormSet(['slug' => $slug])
        ->fillForm(['title' => $title = fake()->sentence()])
        ->assertFormSet(function (array $state) use ($title) {
            expect($state['slug'])->not->toBe(\Illuminate\Support\Str::slug($title));
        })
        ->assertSuccessful();
})->with('titles');
