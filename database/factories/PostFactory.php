<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    protected array $tags = [
        'p' => null,
        'ul' => null,
        'ol' => null,
        'li' => ['-', '+', '*'],
        'strong' => '**',
        'i' => '*',
        'h1' => '#',
        'h2' => '##',
        'h3' => '###',
        'h4' => '####',
        'h5' => '#####',
        'h6' => '######',
        'blockquote' => '>',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->realText(80);

        return [
            'title' => trim($title, ' .'),
            'slug' => Str::slug($title),
            'content' => $this->fakeHtml(),
        ];
    }

    public function fakeHtml(): string
    {
        $content = $this->createElement(trim(fake()->sentence(random_int(5, 8)), ' .'), 'h2');

        foreach (range(1, random_int(2, 10)) as $_) {
            $paragraphs = fake()->paragraphs(random_int(1, 5));

            foreach ($paragraphs as $paragraph) {
                $content .= $this->createElement($paragraph, 'p');
            }

            if (fake()->boolean()) {
                $list = '';

                foreach (range(1, random_int(1, 7)) as $_) {
                    $list .= $this->createElement(trim(fake()->sentence(random_int(5, 8)), ' .'), 'li');
                }

                $content .= $this->createElement($list, fake()->randomElement(['ul', 'ol']));
            }

            if (fake()->boolean(random_int(10, 25))) {
                $content .= $this->createElement(fake()->paragraph(4), 'blockquote');
            }

            $heading = fake()->randomElement(['h2', 'h3', 'h4', 'h5', 'h6']);
            $content .= $this->createElement(trim(fake()->sentence(random_int(5, 8)), ' .'), $heading);

            $content .= $this->createElement(fake()->paragraph(1), 'p');
        }

        return $content;
    }

    public function fakeMarkdown(): string
    {
        $newLines = "\n\n";

        $content = $this->createElement(trim(fake()->sentence(random_int(5, 8)), ' .'), 'h2', true).$newLines;

        foreach (range(1, random_int(2, 10)) as $_) {
            $paragraphs = fake()->paragraphs(random_int(1, 5));

            foreach ($paragraphs as $paragraph) {
                $content .= $this->createElement($paragraph, 'p', true).$newLines;
            }

            if (fake()->boolean()) {
                $list = '';

                foreach (range(1, random_int(1, 7)) as $_) {
                    $list .= $this->createElement(trim(fake()->sentence(random_int(5, 8)), ' .'), 'li', true)."\n";
                }

                $content .= $list ? "$list\n" : '';
            }

            if (fake()->boolean(random_int(10, 25))) {
                $content .= $this->createElement(fake()->paragraph(4), 'blockquote', true).$newLines;
            }

            $heading = fake()->randomElement(['h2', 'h3', 'h4']);
            $content .= $this->createElement(trim(fake()->sentence(random_int(5, 8)), ' .'), $heading, true).$newLines;

            $content .= $this->createElement(fake()->paragraph(1), 'p', true).$newLines;
        }

        return $content;
    }

    public function withHtmlContent(): static
    {
        return $this->state(fn () => ['content' => $this->fakeHtml()]);
    }

    public function withMarkdownContent(): static
    {
        return $this->state(fn () => ['content' => $this->fakeMarkdown()]);
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

    protected function createElement(string $text, string $tag, bool $asMarkdown = false): string
    {
        if (! array_key_exists($tag, $this->tags)) {
            throw new \InvalidArgumentException("Tag '$tag' is not available.");
        }

        if ($asMarkdown) {
            $tag = $this->tags[$tag];

            if ($tag === null) {
                return $text;
            }

            $tag = is_array($tag) ? fake()->randomElement($tag) : $tag;
        }

        return $asMarkdown ? "$tag $text" : "<$tag>$text</$tag>";
    }
}
