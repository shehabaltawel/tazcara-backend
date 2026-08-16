<?php

namespace App\Http\Resources\Api\V1\Admin;

use App\Models\Seat;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Bus Resource
 */
class BusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'class' => $this->class,
            'plate_number' => $this->plate_number,
            'seats' => $this->whenLoaded('seats', fn () => $this->seats->map(
                fn (Seat $seat) => [
                    'id' => $seat->uuid,
                    'code' => $seat->code,
                ]
            )),
        ];
    }
}
