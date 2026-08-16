<?php

namespace App\Http\Resources\Api\V1;

use App\Http\Resources\Api\V1\Admin\BusResource;
use App\Http\Resources\Api\V1\Admin\CityResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'status' => $this->status->value,
            'price' => $this->price,
            'seat' => $this->seat ? [
                'id' => $this->seat->uuid,
                'code' => $this->seat->code,
            ] : null,
            'trip' => $trip ? [
                'id' => $trip->uuid,
                'departure_timestamp' => $trip->departure_timestamp,
                'arrival_timestamp' => $trip->arrival_timestamp,
            ] : null,
            'bus' => $trip?->bus ? BusResource::make($trip->bus) : null,
            'from_city' => $this->fromTripCity?->city ? CityResource::make($this->fromTripCity->city) : null,
            'to_city' => $this->toTripCity?->city ? CityResource::make($this->toTripCity->city) : null,
            'booked_at' => $this->created_at,
        ];
    }
}
