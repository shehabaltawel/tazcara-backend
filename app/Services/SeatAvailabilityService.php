<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\TripCity;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Seat Availability Service
 *
 * Owns the leg-overlap rule so the trip-search and booking flows
 * share one source of truth. Every public method is written to run
 * in a fixed, small number of queries regardless of how many trips
 * or seats are involved — no query inside a loop over trips/seats.
 */
class SeatAvailabilityService
{
    /**
     * The trip's stops for the requested cities together with their
     * sequence positions, or null when the leg is invalid (a city
     * is not a stop, or the cities are not in order).
     */
    public function legStops(Trip $trip, string $fromCityUuid, string $toCityUuid): ?array
    {
        $from = $trip->tripCities->firstWhere('city.uuid', $fromCityUuid);
        $to = $trip->tripCities->firstWhere('city.uuid', $toCityUuid);

        if (! $from || ! $to || $from->sequence >= $to->sequence) {
            return null;
        }

        return [
            'from_trip_city' => $from,
            'to_trip_city' => $to,
            'from_sequence' => $from->sequence,
            'to_sequence' => $to->sequence,
        ];
    }

    /**
     * Available seats for a batch of trips (e.g. search results) in two
     * queries total — one for seats, one for bookings — regardless of
     * how many trips are in the batch. Avoids the N+1 of a per-trip
     * availability query in a loop.
     */
    public function availableSeatsForMany(Collection $trips, array $legsByTripId): array
    {
        $seatsByBusId = Seat::query()
            ->whereIn('bus_id', $trips->pluck('bus_id')->unique())
            ->orderBy('code')
            ->get()
            ->groupBy('bus_id');

        $bookingsByTripId = $this->confirmedBookingsByTripId($trips);

        return $trips->mapWithKeys(function (Trip $trip) use ($legsByTripId, $seatsByBusId, $bookingsByTripId): array {
            $leg = $legsByTripId[$trip->id] ?? null;

            if ($leg === null) {
                return [$trip->id => new Collection];
            }

            $takenSeatIds = $this->takenSeatIds(
                $bookingsByTripId->get($trip->id, new SupportCollection),
                $trip->tripCities->pluck('sequence', 'id'),
                $leg['from_sequence'],
                $leg['to_sequence']
            );

            $seats = $seatsByBusId->get($trip->bus_id, new Collection);

            return [$trip->id => $seats->whereNotIn('id', $takenSeatIds)->values()];
        })->all();
    }

    /**
     * Seats from the given set that are unavailable for the requested
     * leg, checked in a single query regardless of seat count. Used at
     * booking time, after the seats have already been locked.
     */
    public function unavailableSeats(Trip $trip, Collection $seats, int $fromSequence, int $toSequence): Collection
    {
        $bookings = $this->confirmedBookings(
            $trip->tripCities->pluck('id')->all(),
            $seats->pluck('id')
        );

        $takenSeatIds = $this->takenSeatIds(
            $bookings,
            $trip->tripCities->pluck('sequence', 'id'),
            $fromSequence,
            $toSequence
        );

        return $seats->whereIn('id', $takenSeatIds)->values();
    }

    /**
     * Confirmed bookings for every trip in the batch, fetched in a
     * single query and grouped by trip id via each trip's
     * already-loaded stops — no per-trip query.
     */
    private function confirmedBookingsByTripId(Collection $trips): SupportCollection
    {
        $tripIdByTripCityId = $trips
            ->flatMap(fn (Trip $trip) => $trip->tripCities->mapWithKeys(
                fn (TripCity $tripCity) => [$tripCity->id => $trip->id]
            ))
            ->all();

        return $this->confirmedBookings(array_keys($tripIdByTripCityId))
            ->groupBy(fn (Booking $booking) => $tripIdByTripCityId[$booking->from_trip_city_id] ?? null);
    }

    /**
     * Confirmed bookings on any of the given trip-city stops, optionally
     * restricted to a set of seat ids.
     */
    private function confirmedBookings(array $tripCityIds, ?SupportCollection $seatIds = null): SupportCollection
    {
        return Booking::query()
            ->confirmed()
            ->whereIn('from_trip_city_id', $tripCityIds)
            ->when($seatIds !== null, fn ($query) => $query->whereIn('seat_id', $seatIds))
            ->get();
    }

    /**
     * Ids of seats taken by confirmed bookings overlapping the leg.
     */
    private function takenSeatIds(
        SupportCollection $bookings,
        SupportCollection $sequenceByTripCityId,
        int $fromSequence,
        int $toSequence
    ): SupportCollection {
        return $bookings
            ->filter(fn (Booking $booking) => $this->bookingOverlaps($booking, $sequenceByTripCityId, $fromSequence, $toSequence))
            ->pluck('seat_id');
    }

    /**
     * Whether a booking occupies seat on any part of the requested
     * half-open leg [fromSequence, toSequence).
     */
    private function bookingOverlaps(
        Booking $booking,
        SupportCollection $sequenceByTripCityId,
        int $fromSequence,
        int $toSequence
    ): bool {
        $bookingFromSequence = $sequenceByTripCityId[$booking->from_trip_city_id] ?? null;
        $bookingToSequence = $sequenceByTripCityId[$booking->to_trip_city_id] ?? null;

        return $bookingFromSequence !== null
            && $bookingToSequence !== null
            && $bookingFromSequence < $toSequence
            && $fromSequence < $bookingToSequence;
    }
}
