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
            'description' => fake()->text(random_int(256, 2048)),
            'meta' => [
                'technologies' => $this->randomTechnologies(),
            ],
        ];
    }

    protected function randomTechnologies(): array
    {
        $techologies = [
            'JS', 'NodeJS', 'BunJS', 'NextJS', 'ReactJS', 'ReactNative',
            'PHP', 'Laravel', 'Symfony', 'CodeIgniter',
            'Python', 'Django', 'Flask',
            'CSS', 'TailwindCSS', 'Bootstrap', 'Styled Components',
            'SQL', 'MySQL', 'SQLite', 'PostgreSQL', 'NoSQL', 'MongoDB',
        ];

        return fake()->randomElements($techologies, random_int(0, count($techologies)));
    }
}
