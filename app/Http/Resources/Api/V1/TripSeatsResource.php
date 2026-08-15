<?php

namespace App\Http\Resources\Api\V1;

use App\Models\City;
use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Collection;

/**
 * Trip Seats Resource
 */
class TripSeatsResource extends JsonResource
{
    private static ?Collection $requestedCities = null;

    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $requested = $this->requestedCities($request);

        return [
            'requested_from_city' => $requested['from'],
            'requested_to_city' => $requested['to'],
            'requested_date' => $request->input('date'),
            'id' => $this->uuid,
            'from_city' => $this->fromCity?->name,
            'to_city' => $this->toCity?->name,
            'bus' => [
                'class' => $this->bus?->class,
                'plate_number' => $this->bus?->plate_number,
            ],
            'departure_timestamp' => $this->departure_timestamp,
            'arrival_timestamp' => $this->arrival_timestamp,
            'available_seats' => $this->availableSeats->map(
                fn (Seat $seat) => [
                    'id' => $seat->uuid,
                    'code' => $seat->code,
                ]
            ),
        ];
    }

    /**
     * Names of the requested cities, resolved once per request.
     *
     * @return array{from: ?string, to: ?string}
     */
    private function requestedCities(Request $request): array
    {
        self::$requestedCities ??= City::query()
            ->whereIn('uuid', [$request->input('from_city'), $request->input('to_city')])
            ->pluck('name', 'uuid');

        return [
            'from' => self::$requestedCities->get($request->input('from_city')),
            'to' => self::$requestedCities->get($request->input('to_city')),
        ];
    }
}
