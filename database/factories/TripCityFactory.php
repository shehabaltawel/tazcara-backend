<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Trip;
use App\Models\TripCity;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TripCity>
 */
class TripCityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'trip_id' => Trip::factory(),
            'city_id' => City::factory(),
            'sequence' => 0,
            'price_from_origin' => 0,
            'departure_timestamp' => now()->addDay()->setTime(7, 0),
            'arrival_timestamp' => now()->addDay()->setTime(8, 0),
        ];
    }
}
