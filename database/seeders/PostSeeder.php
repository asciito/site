<?php

namespace Database\Seeders;

use App\Models\Post;
use Closure;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PostSeeder extends Seeder
{
    public function run(int $total): void
    {
        $this->task('Creating Posts', static function () use ($total): void {
            $current = now();

            foreach (range(1, $total) as $_) {
                Carbon::setTestNow($current->clone()->subDays(random_int(1, 50 + $total)));

                tap(random_int(0, 1), function (bool $shouldPublish) {
                    $factory = Post::factory();

                    if ($shouldPublish) {
                        $factory = $factory->published();
                    }

                    $factory->create();
                });

            }

            Carbon::setTestNow($current);
        });
    }

    public function task(string $description, Closure $task): void
    {
        \Livewire\invade($this->command)->components->task($description, $task);
    }
}
