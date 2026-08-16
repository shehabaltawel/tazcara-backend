<?php

use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\Feature\Services\CreatesTrips;

uses(CreatesTrips::class);

beforeEach(function (): void {
    $this->resetCities();
    Sanctum::actingAs(User::factory()->create());
});

it('exposes the city uuids needed to build a booking payload', function (): void {
    $trip = $this->standardTrip();
    $from = $this->stop($trip, 'CAI')->city;
    $to = $this->stop($trip, 'ASY')->city;

    $this->getJson('/api/v1/trips/available-seats?'.http_build_query([
        'from_city' => $from->uuid,
        'to_city' => $to->uuid,
        'date' => $trip->departure_timestamp->toDateString(),
    ]))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $trip->uuid)
        ->assertJsonPath('data.0.requested_from_city.id', $from->uuid)
        ->assertJsonPath('data.0.requested_to_city.id', $to->uuid)
        ->assertJsonPath('data.0.requested_from_city.name', 'CAI')
        ->assertJsonPath('data.0.requested_to_city.name', 'ASY')
        ->assertJsonPath('data.0.requested_date', $trip->departure_timestamp->toDateString())
        ->assertJsonCount(2, 'data.0.available_seats')
        ->assertJsonPath('data.0.available_seats.0.code', 'S1')
        ->assertJsonPath('data.0.available_seats.1.code', 'S2');
});

it('returns a clean 404 envelope for an unknown trip', function (): void {
    $this->postJson('/api/v1/trips/'.(string) Str::uuid().'/book-seats', [
        'seats' => ['anything'],
        'from_city' => 'anything',
        'to_city' => 'anything',
        'date' => '2026-08-17',
    ])
        ->assertNotFound()
        ->assertJson([
            'error' => true,
            'message' => 'Resource not found',
            'data' => [],
        ])
        ->assertJsonMissing(['debug' => true]);
});
