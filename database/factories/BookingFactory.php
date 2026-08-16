<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Enums\BookingStatusEnum;
use App\Models\Seat;
use App\Models\TripCity;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'seat_id' => Seat::factory(),
            'from_trip_city_id' => TripCity::factory(),
            'to_trip_city_id' => TripCity::factory(),
            'price' => 100,
            'status' => BookingStatusEnum::CONFIRMED,
        ];
    }
}
