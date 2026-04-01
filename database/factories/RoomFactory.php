<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Hall ' . $this->faker->unique()->numberBetween(1, 10),
            'type' => $this->faker->randomElement(['Normal', 'VIP']),
            'capacity' => 20,
        ];
    }
}
