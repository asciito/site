<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use function Pest\Laravel\options;

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
            '--email' => 'example@test.com',
            '--password' => 'password',
        ]);
    }
}
