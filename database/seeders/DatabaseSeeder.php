<?php

namespace Database\Seeders;

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
        ]);
    }

    protected function createUser(): void
    {
        if (User::count() !== 0) {
            \Livewire\invade($this->command)->components->warn('Attempting to create a user failed because a user already exists.');

            return;
        }

        $this->seedWithPost();

        $this->seedWithMessages();
    }

    protected function seedWithPost(): void
    {
        $this->command->call('make:filament-user', [
            '--name' => 'Test User',
            '--email' => 'example@test.com',
            '--password' => 'password',
        ]);
    }

    protected function seedWithMessages(): void
    {
        $this->call(ContactSeeder::class);
    }
}
