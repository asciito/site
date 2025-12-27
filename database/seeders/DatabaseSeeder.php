<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\JobExperience;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->isProduction()) {
            $this->command->error('You cannot run this command in production');

            return;
        }

        $this->createUser();

        $this->call([
            PostSeeder::class,
            ContactSeeder::class,
        ]);
    }

    protected function createUser(): void
    {
        if (User::count() !== 0) {
            \Livewire\invade($this->command)->components->warn('Attempting to create a user failed because a user already exists.');

            return;
        }

        $this->command->call('make:filament-user', [
            '--name' => 'Test User',
            '--email' => 'test@example.com',
            '--password' => 'password',
        ]);

        $user = User::first();

        $categories = Category::factory($max = random_int(2, 10))->create();

        $experiences = JobExperience::factory()
            ->count(10)
            ->for($user)
            ->create();

        foreach ($experiences as $experience) {
            $experience
                ->categories()
                ->attach($categories->random(random_int(1, $max)));
        }
    }
}
