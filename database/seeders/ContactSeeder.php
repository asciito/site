<?php

namespace Database\Seeders;

use App\Models\Contact;
use Closure;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(int $total): void
    {
        $this->task('Creating messages', static function () use ($total) {
            Contact::factory()
                ->count(10)
                ->hasMessages(random_int(5, $total))
                ->create();
        });
    }

    public function task(string $description, Closure $task): void
    {
        \Livewire\invade($this->command)->components->task($description, $task);
    }
}
