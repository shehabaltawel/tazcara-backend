<?php

namespace Database\Factories;

use App\Models\Bus;
use App\Models\City;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bus_id' => Bus::factory(),
            'from_city_id' => City::factory(),
            'to_city_id' => City::factory(),
            'departure_timestamp' => now()->addDay()->setTime(7, 0),
            'arrival_timestamp' => now()->addDay()->setTime(14, 0),
        ];
    }
}