<?php

use App\Models\Booking;
use App\Models\Bus;
use App\Models\Seat;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Tests\Feature\Services\CreatesTrips;

uses(CreatesTrips::class);

beforeEach(function (): void {
    $this->resetCities();
    $this->bookingService = app(BookingService::class);
    $this->user = User::factory()->create();
});

it('is atomic: books nothing when any of the requested seats is taken', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1', 'S2'], 'CAI', 'ASY')))
        ->toThrow(ConflictHttpException::class);

    expect(Booking::count())->toBe(1);
});

it('allows re-booking the same seat on a non-overlapping leg', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    $bookings = $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1'], 'MNY', 'ASY'));

    expect($bookings)->toHaveCount(1)
        ->and(Booking::count())->toBe(2);
});

it('rejects a seat that belongs to a different bus', function (): void {
    $trip = $this->standardTrip();
    $foreignSeat = Seat::factory()->create(['bus_id' => Bus::factory()]);

    $data = $this->bookingData($trip, ['S1'], 'CAI', 'ASY');
    $data['seats'] = [$foreignSeat->uuid];

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $data))
        ->toThrow(InvalidArgumentException::class);

    expect(Booking::count())->toBe(0);
});

it('rejects an invalid leg and a wrong departure date', function (): void {
    $trip = $this->standardTrip();

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1'], 'ASY', 'CAI')))
        ->toThrow(InvalidArgumentException::class);

    $data = $this->bookingData($trip, ['S1'], 'CAI', 'ASY');
    $data['date'] = $trip->departure_timestamp->copy()->addDay()->toDateString();

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $data))
        ->toThrow(InvalidArgumentException::class);
});

it('throws when a requested seat does not exist', function (): void {
    $trip = $this->standardTrip();

    $data = $this->bookingData($trip, ['S1'], 'CAI', 'ASY');
    $data['seats'] = [(string) Str::uuid()];

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $data))
        ->toThrow(ModelNotFoundException::class);

    expect(Booking::count())->toBe(0);
});

it('charges the difference between the leg stop prices', function (): void {
    $trip = $this->standardTrip();

    $fullLeg = $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S1'], 'CAI', 'ASY'));
    $shortLeg = $this->bookingService->bookMany($trip, $this->user, $this->bookingData($trip, ['S2'], 'CAI', 'FYM'));

    expect((float) $fullLeg->first()->price)->toBe(140.0)
        ->and((float) $shortLeg->first()->price)->toBe(50.0);
});

it('replays the original booking on an idempotent retry and rejects key reuse', function (): void {
    $trip = $this->standardTrip();
    $data = $this->bookingData($trip, ['S1', 'S2'], 'CAI', 'ASY');

    $first = $this->bookingService->bookMany($trip, $this->user, $data, 'retry-key');
    $replay = $this->bookingService->bookMany($trip, $this->user, $data, 'retry-key');

    expect($replay->pluck('id')->all())->toBe($first->pluck('id')->all())
        ->and(Booking::count())->toBe(2);

    $differentLeg = $this->bookingData($trip, ['S1'], 'CAI', 'FYM');

    expect(fn () => $this->bookingService->bookMany($trip, $this->user, $differentLeg, 'retry-key'))
        ->toThrow(ConflictHttpException::class, 'Idempotency key already used');

    expect(Booking::count())->toBe(2);
});
