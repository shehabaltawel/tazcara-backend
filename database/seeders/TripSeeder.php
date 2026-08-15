<?php

namespace Database\Seeders;

use App\Models\Bus;
use App\Models\City;
use App\Models\Trip;
use Carbon\CarbonInterface;
use Illuminate\Database\Seeder;

/**
 * A class seeds the trips and their stops (trip_cities).
 */
class TripSeeder extends Seeder
{
    /**
     * Seed the trips and their stops (trip_cities).
     */
    public function run(): void
    {
        $this->createTrip(
            cities: ['CAI', 'FYM', 'MNY', 'ASY'],
            prices: [0, 50, 90, 140],
            departsAt: now()->addDay()->setTime(7, 0),
            hoursBetweenStops: [0, 2, 4.5, 7],
            bus: Bus::first(),
        );

        $this->createTrip(
            cities: ['CAI', 'TNT', 'ZAG', 'DMT', 'KFS', 'ALX'],
            prices: [0, 70, 120, 180, 240, 300],
            departsAt: now()->addDay()->setTime(9, 0),
            hoursBetweenStops: [0, 3, 6, 9, 12, 15],
            bus: Bus::skip(1)->first() ?? Bus::first(),
        );
    }

    /**
     * Creates one trip plus its ordered stops (trip_cities).
     * $cities, $prices, $hoursBetweenStops are parallel arrays — same index
     * = same stop. Keeps the "shape" of a trip in one obvious place instead
     * of scattered per-stop objects.
     */
    private function createTrip(
        array $cities,
        array $prices,
        CarbonInterface $departsAt,
        array $hoursBetweenStops,
        Bus $bus,
    ): void {
        $stations = City::whereIn('code', $cities)
            ->get()
            ->sortBy(fn ($city) => array_search($city->code, $cities))
            ->values();

        $trip = Trip::firstOrCreate([
            'bus_id' => $bus->id,
            'from_city_id' => $stations->first()->id,
            'to_city_id' => $stations->last()->id,
        ], [
            'departure_timestamp' => $departsAt,
            'arrival_timestamp' => $departsAt->copy()->addHours(end($hoursBetweenStops)),
        ]);

        $stations->each(fn ($city, $sequence) => $trip->tripCities()->updateOrCreate(
            ['city_id' => $city->id],
            [
                'sequence' => $sequence,
                'price_from_origin' => $prices[$sequence],
                'departure_timestamp' => $departsAt->copy()->addHours($hoursBetweenStops[$sequence]),
                'arrival_timestamp' => $departsAt->copy()->addHours($hoursBetweenStops[$sequence]),
            ]
        ));
    }
}
