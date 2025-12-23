<?php

namespace Database\Factories;

use App\Models\JobExperience;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobExperience>
 */
class JobExperienceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start_date = fake()->date('2018-01-01 00:00:00');

        return [
            'title' => fake()->jobTitle(),
            'description' => fake()->text(),
        ];
    }
}
