<?php

use App\Models\City;
use App\Services\TripService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Tests\Feature\Services\CreatesTrips;

uses(CreatesTrips::class);

beforeEach(function (): void {
    $this->resetCities();
    $this->tripService = app(TripService::class);
});

it('returns matching trips with only the seats still free on the leg', function (): void {
    $trip = $this->standardTrip();
    $this->bookSeat($trip, 'S1', 'CAI', 'MNY');

    $trips = $this->searchTrips($trip, 'CAI', 'ASY');

    expect($trips)->toHaveCount(1)
        ->and($trips->first()->availableSeats->pluck('code')->all())->toBe(['S2']);
});

it('excludes trips whose stops are in the wrong order', function (): void {
    $trip = $this->standardTrip();

    expect($this->searchTrips($trip, 'ASY', 'CAI'))->toBeEmpty();
});

it('excludes trips that do not serve both cities', function (): void {
    $trip = $this->makeTrip([
        ['name' => 'CAI', 'price' => 0],
        ['name' => 'FYM', 'price' => 50],
    ]);
    $asyncity = City::factory()->create(['name' => 'ASY', 'code' => 'ASY']);

    expect($this->tripService->getAvailableTripSeats([
        'from_city' => $this->stop($trip, 'FYM')->city->uuid,
        'to_city' => $asyncity->uuid,
        'date' => $trip->departure_timestamp->toDateString(),
    ]))->toBeEmpty();
});

it('throws when a requested city does not exist', function (): void {
    $trip = $this->standardTrip();

    expect(fn () => $this->tripService->getAvailableTripSeats([
        'from_city' => (string) Str::uuid(),
        'to_city' => $this->stop($trip, 'CAI')->city->uuid,
        'date' => $trip->departure_timestamp->toDateString(),
    ]))->toThrow(ModelNotFoundException::class);
});

it('is deterministic across repeated calls', function (): void {
    $trip = $this->standardTrip();

    $first = $this->searchTrips($trip, 'CAI', 'ASY');
    $second = $this->searchTrips($trip, 'CAI', 'ASY');

    expect($second->pluck('id')->all())->toBe($first->pluck('id')->all())
        ->and($second->first()->availableSeats->pluck('id')->all())
        ->toBe($first->first()->availableSeats->pluck('id')->all());
});
