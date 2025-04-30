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
            'title' => $title = fake()->unique()->realText(80),
            'slug' => Str::slug($title),
            'content' => $this->fakeMarkdown(),
        ];
    }

    protected function fakeMarkdown(): string
    {
        $content = '';

        $content .= '## '.fake()->sentence(random_int(5, 8));

        foreach (range(1, random_int(2, 10)) as $_) {
            $paragraphs = fake()->paragraphs(random_int(1, 5));

            foreach ($paragraphs as $paragraph) {
                $content .= "\n\n" . $paragraph;
            }

            if (fake()->boolean()) {

                foreach (range(1, random_int(1, 7)) as $_) {
                    $content .= "\n\n" . fake()->randomElement(['*', '-', '+']) . ' ' . fake()->sentence(random_int(5, 8));
                }
            }

            $content .= "\n\n" . fake()->randomElement(['###', '####']) . ' ' . fake()->sentence(random_int(5, 8));

            $content .= "\n\n" . fake()->paragraph(1);
        }

        return $content;
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
