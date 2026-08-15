<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * Booking Service
 */
class BookingService
{
    public function __construct(private readonly SeatAvailabilityService $seatAvailability) {}

    /**
     * Book the given seats on the given trip for the requested leg.
     * All seats are booked atomically: either every seat is booked or none is.
     *
     * @throws Exception
     * @throws ModelNotFoundException
     */
    public function bookMany(Trip $trip, User $user, array $data): Collection
    {
        $trip->load(['tripCities' => fn ($query) => $query->orderBy('sequence')->with('city')]);

        $seats = Seat::whereIn('uuid', $data['seats'])->get();

        throw_if(
            count($data['seats']) !== $seats->count(),
            fn () => new ModelNotFoundException('One or more seats not found')
        );

        throw_if(
            $trip->departure_timestamp->toDateString() !== $data['date'],
            fn () => new InvalidArgumentException('Trip does not depart on the given date')
        );

        $leg = $this->seatAvailability->legStops($trip, $data['from_city'], $data['to_city']);

        throw_if(
            $leg === null,
            fn () => new InvalidArgumentException('Invalid trip leg')
        );

        $price = (float) $leg['to_trip_city']->price_from_origin
            - (float) $leg['from_trip_city']->price_from_origin;

        return DB::transaction(function () use ($trip, $user, $seats, $leg, $price): Collection {
            $lockedSeats = Seat::query()
                ->whereIn('id', $seats->pluck('id'))
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            throw_if(
                $lockedSeats->contains(fn (Seat $seat) => $seat->bus_id !== $trip->bus_id),
                fn () => new InvalidArgumentException('One or more seats do not belong to this trip')
            );

            $unavailable = $lockedSeats->reject(
                fn (Seat $seat) => $this->seatAvailability->isSeatAvailable(
                    $trip,
                    $seat,
                    $leg['from_sequence'],
                    $leg['to_sequence']
                )
            );

            throw_if(
                $unavailable->isNotEmpty(),
                fn () => new Exception(
                    'Seat(s) '.$unavailable->pluck('code')->implode(', ').' are no longer available for the requested leg'
                )
            );

            $bookingIds = $lockedSeats->map(
                fn (Seat $seat) => Booking::create([
                    'user_id' => $user->id,
                    'seat_id' => $seat->id,
                    'from_trip_city_id' => $leg['from_trip_city']->id,
                    'to_trip_city_id' => $leg['to_trip_city']->id,
                    'price' => $price,
                    'status' => 'confirmed',
                ])->id
            );

            return Booking::query()
                ->whereIn('id', $bookingIds)
                ->with(['seat', 'fromTripCity.city', 'toTripCity.city', 'fromTripCity.trip.bus'])
                ->get();
        });
    }
}
