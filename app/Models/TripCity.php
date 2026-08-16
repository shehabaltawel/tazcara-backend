<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['trip_id', 'city_id', 'sequence', 'price_from_origin', 'departure_timestamp', 'arrival_timestamp'])]
#[RouteKey('uuid')]

/**
 * TripCity Model
 */
class TripCity extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'price_from_origin' => 'decimal:2',
            'departure_timestamp' => 'datetime',
            'arrival_timestamp' => 'datetime',
        ];
    }

    /**
     * Get the trip for the trip city.
     */
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    /**
     * Get the city for the trip city.
     */
    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
