<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Admin\BusResource;
use App\Http\Resources\Api\V1\Admin\CityResource;
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
            'requested_from_city' => $this->whenLoaded('requestedFromCity', fn () => CityResource::make($this->requestedFromCity)),
            'requested_to_city' => $this->whenLoaded('requestedToCity', fn () => CityResource::make($this->requestedToCity)),
            'requested_date' => $this->requested_date,
            'id' => $this->uuid,
            'from_city' => $this->whenLoaded('fromCity', fn () => CityResource::make($this->fromCity)),
            'to_city' => $this->whenLoaded('toCity', fn () => CityResource::make($this->toCity)),
            'bus' => $this->whenLoaded('bus', fn () => BusResource::make($this->bus)),
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
