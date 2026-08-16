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
     */
    public function getAvailableTripSeats(array $data): Collection
    {
        $fromCity = City::where('uuid', $data['from_city'])->firstOrFail();
        $toCity = City::where('uuid', $data['to_city'])->firstOrFail();

        [$trips, $legsByTripId] = $this->matchingTripsWithLegs($fromCity, $toCity, $data['date']);

        $seatsByTripId = $this->seatAvailability->availableSeatsForMany($trips, $legsByTripId);

        return $trips->each(
            fn (Trip $trip) => $trip
                ->setRelation('availableSeats', $seatsByTripId[$trip->id] ?? new Collection)
                ->setRelation('requestedFromCity', $fromCity)
                ->setRelation('requestedToCity', $toCity)
                ->setAttribute('requested_date', $data['date'])
        );
    }

    /**
     * Trips on the given date whose ordered stops include both cities in
     * the right sequence, paired with each trip's already-computed leg —
     * legStops() is evaluated exactly once per trip, not recomputed later.
     */
    private function matchingTripsWithLegs(City $fromCity, City $toCity, string $date): array
    {
        $trips = Trip::query()
            ->departingOn($date)
            ->servingCities($fromCity->id, $toCity->id)
            ->with(['fromCity', 'toCity', 'bus'])
            ->with(['tripCities' => fn ($query) => $query->orderBy('sequence')->with('city')])
            ->get();

        $legsByTripId = $trips
            ->mapWithKeys(fn (Trip $trip) => [
                $trip->id => $this->seatAvailability->legStops($trip, $fromCity->uuid, $toCity->uuid),
            ])
            ->reject(fn (?array $leg) => $leg === null)
            ->all();

        return [
            $trips->filter(fn (Trip $trip) => isset($legsByTripId[$trip->id]))->values(),
            $legsByTripId,
        ];
    }
}
