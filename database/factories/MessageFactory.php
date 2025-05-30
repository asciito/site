<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Site\Models\Message>
 */
class MessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message' => $this->faker->text(random_int(32, 512)),
            'status' => \App\MessageStatusEnum::UNREAD,
        ];
    }

    public function markAsRead(): self
    {
        return $this->state([
            'status' => \App\MessageStatusEnum::READ,
        ]);
    }
}
