<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            return;
        }

        $this->createUser();

        $this->createPosts(50);
    }

    protected function createUser(): void
    {
        if (User::count() !== 0) {
            $this->command->error('There\'s already a user, you cannot another');

            return;
        }

        $this->command->call('make:filament-user');
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
