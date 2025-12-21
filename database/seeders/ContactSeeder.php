<?php

namespace Database\Seeders;

use App\Models\Contact;
use Illuminate\Database\Seeder;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Livewire\invade($this->command)->components->task('Creating messages', function () {
            Contact::factory(50)
                ->hasMessages(random_int(1, 10))
                ->create();
        });
    }
}
