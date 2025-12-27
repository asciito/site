<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\JobExperience;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class JobExperienceSeeder extends Seeder
{
    public function run(User $user, int $total): void
    {
        /** @var ?JobExperience $latest */
        $latest = null;
        $categories = $this->createCategories(10);
        $experiences = JobExperience::factory($total = random_int(5, max($total, 10)))
            ->sequence(
                ...array_map(
                    static fn () => [
                        'date_range_as_relative' => fake()->boolean(),
                        'working_here' => fake()->boolean(),
                    ],
                    array_fill(0, $total, false),
                )
            )
            ->for($user)
            ->create();

        $experiences->each(static function ($experience) use ($categories): void {
            $max = $categories->count();
            $categories = $categories->random(random_int(1, $max))->pluck('id');

            $experience
                ->categories()
                ->attach($categories->toArray());
        });

        $days = (int) $experiences->first()->start_date->clone()->diff(now())->days;
        $slots = $experiences->count();

        $window = (int) ($days / $slots);

        $experiences->each(static function ($experience) use (&$latest, $window): void {
            if ($latest !== null) {
                $experience->start_date = $latest->end_date->clone()->addDay();
            }

            $experience->end_date = $experience->start_date->clone()->addDays($window);

            $latest = $experience;
        })->each->save();
    }

    protected function createCategories(int $total): Collection
    {
        return Category::factory(random_int(5, max($total, 10)))->create()->collect();
    }
}
