<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $title = fake()->unique()->text(80),
            'slug' => Str::slug($title),
            'content' => collect(fake()->paragraphs(random_int(5, 20)))
                ->map(function (string $p) {
                    return "<p>$p</p>";
                })
                ->join("\n"),
        ];
    }

    public function published(): static
    {
        return $this->afterCreating(fn (Post $post) => $post->publish());
    }

    public function archived(): static
    {
        return $this->afterCreating(function (Post $post) {
            $post->archive();
        });
    }

    public function dontSyncSlug(): static
    {
        return $this->state(fn (array $attributes) => ['slug' => Str::slug(fake()->unique()->sentence())]);
    }
}
