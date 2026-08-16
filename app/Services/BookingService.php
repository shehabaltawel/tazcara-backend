<?php

namespace App\Services;

use App\Enums\BookingStatusEnum;
use App\Models\Booking;
use App\Models\Seat;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

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
     * When an idempotency key is provided and a confirmed booking for the same
     * user, seats and leg already exists, that booking is replayed instead of
     * creating a new one. Reusing a key for a different request throws.
     *
     * @throws ConflictHttpException
     * @throws InvalidArgumentException
     * @throws ModelNotFoundException
     */
    public function bookMany(Trip $trip, User $user, array $data, ?string $idempotencyKey = null): Collection
    {
        $trip->load(['tripCities' => fn ($query) => $query->orderBy('sequence')->with('city')]);

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

        return DB::transaction(function () use ($trip, $user, $data, $leg, $price, $idempotencyKey): Collection {
            if ($idempotencyKey !== null) {
                $existing = $this->idempotentBookings($trip, $user, $data, $leg, $idempotencyKey);

                if ($existing !== null) {
                    return $existing;
                }
            }

            $lockedSeats = Seat::query()
                ->whereIn('uuid', $data['seats'])
                ->orderBy('id')
                ->lockForUpdate()
                ->get();

            throw_if(
                $lockedSeats->count() !== count($data['seats']),
                fn () => new ModelNotFoundException('One or more seats not found')
            );

            throw_if(
                $lockedSeats->contains(fn (Seat $seat) => $seat->bus_id !== $trip->bus_id),
                fn () => new InvalidArgumentException('One or more seats do not belong to this trip')
            );

            $unavailable = $this->seatAvailability->unavailableSeats(
                $trip,
                $lockedSeats,
                $leg['from_sequence'],
                $leg['to_sequence']
            );

            throw_if(
                $unavailable->isNotEmpty(),
                fn () => new ConflictHttpException(
                    'Seat(s) '.$unavailable->pluck('code')->implode(', ').' are no longer available for the requested leg'
                )
            );

            try {
                $bookingIds = $lockedSeats->map(
                    fn (Seat $seat) => Booking::create([
                        'user_id' => $user->id,
                        'seat_id' => $seat->id,
                        'from_trip_city_id' => $leg['from_trip_city']->id,
                        'to_trip_city_id' => $leg['to_trip_city']->id,
                        'price' => $price,
                        'status' => BookingStatusEnum::CONFIRMED,
                        'idempotency_key' => $idempotencyKey,
                    ])->id
                );
            } catch (QueryException $exception) {
                if ($idempotencyKey !== null && $exception->getCode() === '23000') {
                    $existing = $this->idempotentBookings($trip, $user, $data, $leg, $idempotencyKey);

                    if ($existing !== null) {
                        return $existing;
                    }
                }

                throw $exception;
            }

            return Booking::query()
                ->whereIn('id', $bookingIds)
                ->with(['seat', 'fromTripCity.city', 'toTripCity.city', 'fromTripCity.trip.bus'])
                ->get();
        });
    }

    /**
     * The user's bookings for the given idempotency key when they replay the
     * same request, or null when no such booking exists yet.
     */
    private function idempotentBookings(Trip $trip, User $user, array $data, array $leg, string $idempotencyKey): ?Collection
    {
        $bookings = Booking::query()
            ->where('user_id', $user->id)
            ->where('idempotency_key', $idempotencyKey)
            ->with(['seat', 'fromTripCity.city', 'toTripCity.city', 'fromTripCity.trip.bus'])
            ->get();

        if ($bookings->isEmpty()) {
            return null;
        }

        $sameLeg = $bookings->first()->from_trip_city_id === $leg['from_trip_city']->id
            && $bookings->first()->to_trip_city_id === $leg['to_trip_city']->id;

        $sameSeats = $bookings->pluck('seat.uuid')->sort()->values()->all()
            === collect($data['seats'])->sort()->values()->all();

        throw_if(
            ! $sameLeg || ! $sameSeats,
            fn () => new ConflictHttpException('Idempotency key already used for a different request')
        );

        return $bookings;
    }
}
