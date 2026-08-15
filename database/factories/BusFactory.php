<?php

namespace Database\Factories;

use App\Models\Bus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bus>
 */
class BusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'class' => fake()->randomElement(['First Class', 'Business', 'Economy']),
            'plate_number' => fake()->unique()->bothify('??-####'),
        ];
    }
}
