<?php

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Seat;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Tests\Feature\Services\CreatesTrips;

uses(CreatesTrips::class);

beforeEach(function (): void {
    $this->bookingService = app(BookingService::class);
    $this->user = User::factory()->create();
});

it('books every requested seat atomically', function (): void {
    $trip = $this->standardTrip();

    $bookings = $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1', 'S2'], 'CAI', 'ASY'));

    expect($bookings)->toHaveCount(2)
        ->and($bookings->every(fn (Booking $booking) => $booking->user_id === $this->user->id))->toBeTrue()
        ->and($bookings->every(fn (Booking $booking) => $booking->status === 'confirmed'))->toBeTrue();
});

it('charges the difference of the price_from_origin of the leg stops', function (): void {
    $trip = $this->standardTrip();

    $bookings = $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1'], 'CAI', 'ASY'));

    expect($bookings->first()->price)->toEqual(140.0);
});

it('throws and books nothing when a seat is already taken', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1'], 'CAI', 'ASY')))
        ->toThrow(\Exception::class, 'S1');

    expect(Booking::count())->toBe(1);
});

it('is atomic when only some of the requested seats are free', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1', 'S2'], 'CAI', 'ASY')))
        ->toThrow(\Exception::class);

    expect(Booking::count())->toBe(1);
});

it('allows re-booking a seat on a non-overlapping leg', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    $bookings = $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1'], 'MNY', 'ASY'));

    expect($bookings)->toHaveCount(1)
        ->and(Booking::count())->toBe(2);
});

it('rejects a reversed leg', function (): void {
    $trip = $this->standardTrip();

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1'], 'ASY', 'CAI')))
        ->toThrow(\InvalidArgumentException::class, 'Invalid trip leg');
});

it('rejects a seat that does not belong to the trip bus', function (): void {
    $trip = $this->standardTrip();
    $foreignSeat = Seat::factory()->create(['bus_id' => Bus::factory()]);

    $data = $this->bookingData($trip, ['S1'], 'CAI', 'ASY');
    $data['seats'] = [$foreignSeat->uuid];

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $data))
        ->toThrow(\InvalidArgumentException::class, 'do not belong to this trip');
});

it('rejects a trip that does not depart on the given date', function (): void {
    $trip = $this->standardTrip();

    $data = $this->bookingData($trip, ['S1'], 'CAI', 'ASY');
    $data['date'] = $trip->departure_timestamp->copy()->addDay()->toDateString();

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $data))
        ->toThrow(\InvalidArgumentException::class, 'does not depart');
});

it('throws a ModelNotFoundException when a seat does not exist', function (): void {
    $trip = $this->standardTrip();

    $data = $this->bookingData($trip, ['S1'], 'CAI', 'ASY');
    $data['seats'] = [(string) Str::uuid()];

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $data))
        ->toThrow(ModelNotFoundException::class);
});