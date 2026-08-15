<?php

namespace Tests\Feature\Services;

use App\Models\Booking;
use App\Models\Bus;
use App\Models\City;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\TripCity;
use App\Models\User;

trait CreatesTrips
{
    /**
     * Build a trip with ordered stops and a fixed seat set.
     *
     * @param  array<int, array{name: string, price: float}>  $stops
     */
    protected function makeTrip(array $stops, int $seatCount = 2): Trip
    {
        $cities = collect($stops)->map(
            fn (array $stop) => City::factory()->create(['name' => $stop['name'], 'code' => $stop['name']])
        );

        $bus = Bus::factory()->create();

        $departsAt = now()->addDay()->setTime(7, 0);

        $trip = Trip::factory()->create([
            'bus_id' => $bus->id,
            'from_city_id' => $cities->first()->id,
            'to_city_id' => $cities->last()->id,
            'departure_timestamp' => $departsAt,
            'arrival_timestamp' => $departsAt->copy()->addHours(count($stops) - 1),
        ]);

        $cities->each(function (City $city, int $sequence) use ($trip, $stops, $departsAt): void {
            TripCity::factory()->create([
                'trip_id' => $trip->id,
                'city_id' => $city->id,
                'sequence' => $sequence,
                'price_from_origin' => $stops[$sequence]['price'],
                'departure_timestamp' => $departsAt->copy()->addHours($sequence),
                'arrival_timestamp' => $departsAt->copy()->addHours($sequence + 1),
            ]);
        });

        for ($i = 1; $i <= $seatCount; $i++) {
            Seat::factory()->create(['bus_id' => $bus->id, 'code' => "S{$i}"]);
        }

        return $trip->load(['tripCities' => fn ($query) => $query->orderBy('sequence')->with('city')]);
    }

    /**
     * The canonical CAI -> FYM -> MNY -> ASY trip.
     */
    protected function standardTrip(int $seatCount = 2): Trip
    {
        return $this->makeTrip([
            ['name' => 'CAI', 'price' => 0],
            ['name' => 'FYM', 'price' => 50],
            ['name' => 'MNY', 'price' => 90],
            ['name' => 'ASY', 'price' => 140],
        ], $seatCount);
    }

    /**
     * Create a booking on the trip for the given seat and leg.
     */
    protected function bookSeat(Trip $trip, string $seatCode, string $fromCity, string $toCity, string $status = 'confirmed'): void
    {
        Booking::factory()->create([
            'user_id' => User::factory(),
            'seat_id' => $this->seat($trip, $seatCode)->id,
            'from_trip_city_id' => $this->stop($trip, $fromCity)->id,
            'to_trip_city_id' => $this->stop($trip, $toCity)->id,
            'status' => $status,
        ]);
    }

    /**
     * Validated booking payload for the given seats and leg.
     *
     * @param  string[]  $seatCodes
     * @return array{seats: string[], from_city: string, to_city: string, date: string}
     */
    protected function bookingData(Trip $trip, array $seatCodes, string $fromCity, string $toCity): array
    {
        return [
            'seats' => collect($seatCodes)->map(fn (string $code) => $this->seat($trip, $code)->uuid)->all(),
            'from_city' => $this->stop($trip, $fromCity)->city->uuid,
            'to_city' => $this->stop($trip, $toCity)->city->uuid,
            'date' => $trip->departure_timestamp->toDateString(),
        ];
    }

    /**
     * The trip stop for the given city name.
     */
    protected function stop(Trip $trip, string $cityName): TripCity
    {
        return $trip->tripCities->firstWhere('city.name', $cityName);
    }

    /**
     * A seat of the trip's bus with the given code.
     */
    protected function seat(Trip $trip, string $code): Seat
    {
        return Seat::where('bus_id', $trip->bus_id)->where('code', $code)->firstOrFail();
    }
}
