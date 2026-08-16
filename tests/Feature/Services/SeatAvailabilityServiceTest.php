<?php

use App\Models\Bus;
use App\Models\Seat;
use App\Services\SeatAvailabilityService;
use Illuminate\Support\Str;
use Tests\Feature\Services\CreatesTrips;

uses(CreatesTrips::class);

beforeEach(function (): void {
    $this->resetCities();
    $this->availability = new SeatAvailabilityService;
});

it('resolves a leg to its ordered stops, or null when the leg is invalid', function (string $from, string $to, ?int $fromSequence, ?int $toSequence): void {
    $trip = $this->standardTrip();

    $leg = $this->availability->legStops(
        $trip,
        $this->stop($trip, $from)->city->uuid,
        $this->stop($trip, $to)->city->uuid
    );

    if ($fromSequence === null) {
        expect($leg)->toBeNull();

        return;
    }

    expect($leg['from_sequence'])->toBe($fromSequence)
        ->and($leg['to_sequence'])->toBe($toSequence);
})->with([
    'full leg' => ['CAI', 'ASY', 0, 3],
    'adjacent leg' => ['CAI', 'FYM', 0, 1],
    'reversed leg' => ['ASY', 'CAI', null, null],
    'same city' => ['CAI', 'CAI', null, null],
]);

it('returns null when a city is not a stop on the trip', function (): void {
    $trip = $this->standardTrip();

    expect($this->availability->legStops($trip, (string) Str::uuid(), $this->stop($trip, 'CAI')->city->uuid))->toBeNull();
});

it('blocks a seat only on legs that overlap the booked segment', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    // CAI -> ASY overlaps the booked CAI -> MNY segment.
    expect($this->availabilityFor($trip, 'CAI', 'ASY')->pluck('code')->all())->toBe(['S2']);

    // MNY -> ASY only touches at MNY: the half-open leg [MNY, ASY) is free.
    expect($this->availabilityFor($trip, 'MNY', 'ASY')->pluck('code')->all())->toBe(['S1', 'S2']);
});

it('scopes bookings to the trip, not the bus', function (): void {
    $bus = Bus::factory()->create();
    $trip = $this->makeTrip([
        ['name' => 'CAI', 'price' => 0],
        ['name' => 'FYM', 'price' => 50],
        ['name' => 'MNY', 'price' => 90],
        ['name' => 'ASY', 'price' => 140],
    ], 2, $bus);
    $otherTrip = $this->tripOnBus($bus, [
        ['name' => 'FYM', 'price' => 0],
        ['name' => 'MNY', 'price' => 40],
        ['name' => 'ASY', 'price' => 90],
    ]);
    $this->bookSeat($trip, 'S1', 'CAI', 'ASY');

    expect($this->availabilityFor($trip, 'CAI', 'ASY')->pluck('code')->all())->toBe(['S2'])
        ->and($this->availabilityFor($otherTrip, 'FYM', 'ASY')->pluck('code')->all())->toBe(['S1', 'S2']);
});

it('ignores cancelled bookings when reporting availability', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'ASY', status: 'cancelled');

    expect($this->availabilityFor($trip, 'CAI', 'ASY')->pluck('code')->all())->toBe(['S1', 'S2']);
});

it('reports the taken seats when re-checking availability at booking time', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    $seats = Seat::where('bus_id', $trip->bus_id)->get();
    $unavailable = $this->availability->unavailableSeats($trip, $seats, 0, 3);

    expect($unavailable->pluck('code')->all())->toBe(['S1']);
});
