<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Request;

/**
 * Booking Resource
 */
class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        $trip = $this->fromTripCity?->trip;

        return [
            'id' => $this->uuid,
            'status' => $this->status,
            'price' => $this->price,
            'seat' => [
                'id' => $this->seat?->uuid,
                'code' => $this->seat?->code,
            ],
            'trip' => [
                'id' => $trip?->uuid,
                'departure_timestamp' => $trip?->departure_timestamp,
                'arrival_timestamp' => $trip?->arrival_timestamp,
            ],
            'bus' => [
                'class' => $trip?->bus?->class,
                'plate_number' => $trip?->bus?->plate_number,
            ],
            'from_city' => $this->fromTripCity?->city?->name,
            'to_city' => $this->toTripCity?->city?->name,
            'booked_at' => $this->created_at,
        ];
    }
}