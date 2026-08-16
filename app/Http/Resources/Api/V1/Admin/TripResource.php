<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Models\TripCity;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Trip Resource
 */
class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'bus' => $this->whenLoaded('bus', fn () => BusResource::make($this->bus)),
            'from_city' => $this->whenLoaded('fromCity', fn () => CityResource::make($this->fromCity)),
            'to_city' => $this->whenLoaded('toCity', fn () => CityResource::make($this->toCity)),
            'departure_timestamp' => $this->departure_timestamp,
            'arrival_timestamp' => $this->arrival_timestamp,
            'stops' => $this->whenLoaded('tripCities', fn () => $this->tripCities
                ->sortBy('sequence')
                ->values()
                ->map(fn (TripCity $stop) => [
                    'sequence' => $stop->sequence,
                    'city' => $stop->city ? CityResource::make($stop->city) : null,
                    'price_from_origin' => $stop->price_from_origin,
                    'departure_timestamp' => $stop->departure_timestamp,
                    'arrival_timestamp' => $stop->arrival_timestamp,
                ])),
        ];
    }
}
