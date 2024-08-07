<?php

namespace Database\Factories;

use App\Models\Post;
use App\Site\Enums\Status;
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
            'title' => $title = fake()->text(random_int(10, 18)),
            'slug' => Str::slug($title),
            'content' => collect(fake()->paragraphs(random_int(3, 5)))
                ->map(fn (string $p) => "<p>$p</p>")
                ->join("\n<br>"),
        ];
    }

    public function published(): static
    {
        return $this->afterCreating(fn (Post $post) => $post->publish());
    }
}
