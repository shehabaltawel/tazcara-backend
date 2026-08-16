<?php

namespace App\Services;

use App\Models\Bus;
use App\Models\City;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Admin Trip Service
 */
class AdminTripService
{
    /**
     * Create a trip with its ordered stops atomically.
     *
     * @throws ConflictHttpException when the bus is already scheduled on an overlapping trip
     */
    public function create(array $data): Trip
    {
        return DB::transaction(function () use ($data): Trip {
            $bus = Bus::where('uuid', $data['bus_id'])->firstOrFail();

            $stops = array_values($data['stops']);
            $origin = $stops[0];
            $destination = $stops[count($stops) - 1];

            $cityIds = City::whereIn('uuid', array_column($stops, 'city_id'))
                ->pluck('id', 'uuid');

            $departure = Carbon::parse($origin['departure_timestamp']);
            $arrival = Carbon::parse($destination['arrival_timestamp']);

            $this->assertBusAvailable($bus, $departure, $arrival);

            $trip = Trip::create([
                'bus_id' => $bus->id,
                'from_city_id' => $cityIds[$origin['city_id']],
                'to_city_id' => $cityIds[$destination['city_id']],
                'departure_timestamp' => $departure,
                'arrival_timestamp' => $arrival,
            ]);

            $trip->tripCities()->createMany(
                collect($stops)->map(function (array $stop, int $sequence) use ($cityIds): array {
                    return [
                        'city_id' => $cityIds[$stop['city_id']],
                        'sequence' => $sequence,
                        'price_from_origin' => $stop['price_from_origin'],
                        'departure_timestamp' => $stop['departure_timestamp'],
                        'arrival_timestamp' => $stop['arrival_timestamp'],
                    ];
                })
            );

            return $trip->load(['bus', 'fromCity', 'toCity', 'tripCities.city']);
        });
    }

    /**
     * Soft delete the given trip.
     */
    public function delete(Trip $trip): void
    {
        $trip->delete();
    }

    /**
     * Refuse to schedule the bus on a second trip with an overlapping window.
     *
     * @throws ConflictHttpException
     */
    private function assertBusAvailable(Bus $bus, Carbon $departure, Carbon $arrival): void
    {
        throw_if(
            Trip::where('bus_id', $bus->id)
                ->where('departure_timestamp', '<', $arrival)
                ->where('arrival_timestamp', '>', $departure)
                ->exists(),
            ConflictHttpException::class,
            'The bus is already scheduled on an overlapping trip'
        );
    }
}
