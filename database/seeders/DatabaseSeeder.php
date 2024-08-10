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

    protected function createUser(): User
    {
        if (User::count() !== 0) {
            return User::first();
        }

        return User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
        ]);
    }

    protected function createPosts(int $total): Collection
    {
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

        return Post::all();
    }
}
