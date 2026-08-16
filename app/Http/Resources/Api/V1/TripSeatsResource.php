<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Trip Seats Resource
 */
class TripSeatsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'requested_from_city' => $this->whenLoaded('requestedFromCity', fn () => $this->requestedFromCity->name),
            'requested_to_city' => $this->whenLoaded('requestedToCity', fn () => $this->requestedToCity->name),
            'requested_date' => $this->requested_date,
            'id' => $this->uuid,
            'from_city' => $this->whenLoaded('fromCity', fn () => $this->fromCity->name),
            'to_city' => $this->whenLoaded('toCity', fn () => $this->toCity->name),
            'bus' => $this->whenLoaded('bus', fn () => [
                'class' => $this->bus->class,
                'plate_number' => $this->bus->plate_number,
            ]),
            'departure_timestamp' => $this->departure_timestamp,
            'arrival_timestamp' => $this->arrival_timestamp,
            'available_seats' => $this->whenLoaded('availableSeats', fn () => $this->availableSeats->map(
                fn (Seat $seat) => [
                    'id' => $seat->uuid,
                    'code' => $seat->code,
                ]
            )),
        ];
    }
}