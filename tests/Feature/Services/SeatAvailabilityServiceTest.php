<?php

use App\Services\SeatAvailabilityService;
use Illuminate\Support\Str;
use Tests\Feature\Services\CreatesTrips;

uses(CreatesTrips::class);

beforeEach(function (): void {
    $this->availability = new SeatAvailabilityService;
});

it('returns every bus seat on a free leg', function (): void {
    $trip = $this->standardTrip();

    expect($this->availability->availableSeatsFor($trip, 0, 3))->toHaveCount(2);
});

it('excludes a seat taken on an overlapping leg', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    $seats = $this->availability->availableSeatsFor($trip, 0, 3);

    expect($seats)->toHaveCount(1)
        ->and($seats->first()->code)->toBe('S2');
});

it('keeps a seat free on a non-overlapping adjacent leg', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    expect($this->availability->availableSeatsFor($trip, 2, 3))->toHaveCount(2);
});

it('is not affected by bookings on other trips', function (): void {
    $firstTrip = $this->standardTrip();
    $otherTrip = $this->standardTrip();
    $this->bookSeat($firstTrip, 'S1', 'CAI', 'ASY');

    expect($this->availability->availableSeatsFor($otherTrip, 0, 3))->toHaveCount(2);
});

it('ignores non-confirmed bookings', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'ASY', status: 'cancelled');

    expect($this->availability->availableSeatsFor($trip, 0, 3))->toHaveCount(2);
});

it('tells whether a seat is free for a leg', function (): void {
    $trip = $this->standardTrip();

    expect($this->availability->isSeatAvailable($trip, $this->seat($trip, 'S1'), 0, 3))->toBeTrue();

    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    expect($this->availability->isSeatAvailable($trip, $this->seat($trip, 'S1'), 0, 3))->toBeFalse()
        ->and($this->availability->isSeatAvailable($trip, $this->seat($trip, 'S1'), 2, 3))->toBeTrue();
});

it('resolves a leg to its ordered stops and sequence positions', function (): void {
    $trip = $this->standardTrip();
    $from = $this->stop($trip, 'CAI');
    $to = $this->stop($trip, 'ASY');

    $leg = $this->availability->legStops($trip, $from->city->uuid, $to->city->uuid);

    expect($leg)->not->toBeNull()
        ->and($leg['from_sequence'])->toBe(0)
        ->and($leg['to_sequence'])->toBe(3)
        ->and($leg['from_trip_city']->id)->toBe($from->id)
        ->and($leg['to_trip_city']->id)->toBe($to->id);
});

it('returns null when the leg is reversed or a city is missing', function (string $fromCity, string $toCity): void {
    $trip = $this->standardTrip();

    $fromUuid = $fromCity === 'MISSING' ? (string) Str::uuid() : $this->stop($trip, $fromCity)->city->uuid;
    $toUuid = $toCity === 'MISSING' ? (string) Str::uuid() : $this->stop($trip, $toCity)->city->uuid;

    expect($this->availability->legStops($trip, $fromUuid, $toUuid))->toBeNull();
})->with([
    'reversed leg' => ['ASY', 'CAI'],
    'missing origin' => ['MISSING', 'CAI'],
    'missing destination' => ['CAI', 'MISSING'],
]);