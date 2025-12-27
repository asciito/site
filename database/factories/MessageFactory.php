<?php

namespace Database\Factories;

use App\MessageStatusEnum;
use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'message' => $this->faker->text(random_int(32, 512)),
            'status' => fake()->randomElement([MessageStatusEnum::READ, MessageStatusEnum::UNREAD]),
        ];
    }
}
