<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $this->createPosts(50);
    }

    protected function createPosts(int $total): Collection
    {
        \Livewire\invade($this->command)->components->task('Creating Posts', function () use ($total) {
            foreach (range(1, $total) as $_) {
                Carbon::setTestNow(now()->subDays(random_int(1, 50 + $total)));

                tap(random_int(0, 1), function (bool $shouldPublish) {
                    $factory = Post::factory();

                    if ($shouldPublish) {
                        $factory = $factory->published();
                    }

                    $factory->create();
                });
            }
        });

        return Post::all();
    }
}
