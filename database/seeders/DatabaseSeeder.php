<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\InteractsWithSeederForTesting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use InteractsWithSeederForTesting;

    public function process($user, $total): void
    {
        if (app()->isProduction()) {
            $this->command->error('You cannot run the base seeder in production');
        }

        $this
            ->call(PostSeeder::class, parameters: ['total' => 50])
            ->call(ContactSeeder::class, parameters: ['total' => 100])
            ->call(JobExperienceSeeder::class, parameters: ['user' => $user, 'total' => $total]);
    }
}
