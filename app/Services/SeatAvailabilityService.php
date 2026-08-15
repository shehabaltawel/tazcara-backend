<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Seat Availability Service
 *
 * Owns the leg-overlap rule so the trip-search and booking flows
 * share one source of truth.
 */
class SeatAvailabilityService
{
    /**
     * Seats of the trip's bus not occupied on the requested leg.
     */
    public function availableSeatsFor(Trip $trip, int $fromSequence, int $toSequence): Collection
    {
        return Seat::query()
            ->where('bus_id', $trip->bus_id)
            ->whereNotIn('id', $this->takenSeatIds($trip, $fromSequence, $toSequence))
            ->orderBy('code')
            ->get();
    }

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
     * Whether the given seat is free for the requested leg.
     */
    public function isSeatAvailable(Trip $trip, Seat $seat, int $fromSequence, int $toSequence): bool
    {
        $overlap = Booking::query()
            ->confirmed()
            ->where('seat_id', $seat->id)
            ->whereHas('fromTripCity', fn ($query) => $query->where('trip_id', $trip->id))
            ->get()
            ->contains(fn (Booking $booking) => $this->bookingOverlaps($booking, $trip, $fromSequence, $toSequence));

        return ! $overlap;
    }

    /**
     * Ids of the trip's seats taken by confirmed bookings overlapping the leg.
     *
     * @return SupportCollection<int, int>
     */
    private function takenSeatIds(Trip $trip, int $fromSequence, int $toSequence): SupportCollection
    {
        return Booking::query()
            ->confirmed()
            ->whereHas('fromTripCity', fn ($query) => $query->where('trip_id', $trip->id))
            ->get()
            ->filter(fn (Booking $booking) => $this->bookingOverlaps($booking, $trip, $fromSequence, $toSequence))
            ->pluck('seat_id');
    }

    /**
     * Whether a booking occupies the seat on any part of the requested
     * half-open leg [fromSequence, toSequence).
     */
    private function bookingOverlaps(Booking $booking, Trip $trip, int $fromSequence, int $toSequence): bool
    {
        $sequenceByTripCityId = $trip->tripCities->pluck('sequence', 'id');

        $bookingFromSequence = $sequenceByTripCityId[$booking->from_trip_city_id] ?? null;
        $bookingToSequence = $sequenceByTripCityId[$booking->to_trip_city_id] ?? null;

        return $bookingFromSequence !== null
            && $bookingToSequence !== null
            && $bookingFromSequence < $toSequence
            && $fromSequence < $bookingToSequence;
    }
}
