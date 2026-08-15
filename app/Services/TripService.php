<?php

namespace App\Services;

use App\Models\City;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;

/**
 * Trip Service
 */
class TripService
{
    public function __construct(private readonly SeatAvailabilityService $seatAvailability) {}

    /**
     * Find trips departing on the given date whose ordered stops contain
     * both cities in the right sequence, with each trip's seats available
     * for the requested leg (from_city -> to_city).
     *
     * @param  array{from_city: string, to_city: string, date: string}  $data
     */
    public function getAvailableTripSeats(array $data): Collection
    {
        $fromCity = City::where('uuid', $data['from_city'])->firstOrFail();
        $toCity = City::where('uuid', $data['to_city'])->firstOrFail();

        return $this->findMatchingTrips($fromCity, $toCity, $data['date'])
            ->each(function (Trip $trip) use ($data): void {
                $leg = $this->seatAvailability->legStops($trip, $data['from_city'], $data['to_city']);

                $trip->setRelation(
                    'availableSeats',
                    $leg === null
                        ? new Collection
                        : $this->seatAvailability->availableSeatsFor($trip, $leg['from_sequence'], $leg['to_sequence'])
                );
            });
    }

    /**
     * Trips on the given date whose ordered stops include both cities
     * in the right sequence.
     */
    private function findMatchingTrips(City $fromCity, City $toCity, string $date): Collection
    {
        return Trip::query()
            ->departingOn($date)
            ->servingCities($fromCity->id, $toCity->id)
            ->with(['fromCity', 'toCity', 'bus'])
            ->with(['tripCities' => fn ($query) => $query->orderBy('sequence')->with('city')])
            ->get()
            ->filter(
                fn (Trip $trip) => $this->seatAvailability->legStops($trip, $fromCity->uuid, $toCity->uuid) !== null
            )
            ->values();
    }
}
