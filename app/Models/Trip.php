<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bus_id', 'from_city_id', 'to_city_id', 'departure_timestamp', 'arrival_timestamp'])]
#[RouteKey('uuid')]

/**
 * Trip Model
 */
class Trip extends Model
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
            'departure_timestamp' => 'datetime',
            'arrival_timestamp' => 'datetime',
        ];
    }

    /**
     * Scope the query to trips departing on the given date.
     */
    #[Scope]
    protected function departingOn(Builder $query, string $date): Builder
    {
        return $query->whereDate('departure_timestamp', $date);
    }

    /**
     * Scope the query to trips whose stops include both cities.
     * Order is not enforced here — use the service layer for leg order.
     */
    #[Scope]
    protected function servingCities(Builder $query, int $fromCityId, int $toCityId): Builder
    {
        return $query
            ->whereHas('tripCities', fn ($query) => $query->where('city_id', $fromCityId))
            ->whereHas('tripCities', fn ($query) => $query->where('city_id', $toCityId));
    }

    /**
     * Get the bus for the trip.
     */
    public function bus(): BelongsTo
    {
        return $this->belongsTo(Bus::class);
    }

    /**
     * Get the origin city for the trip.
     */
    public function fromCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'from_city_id');
    }

    /**
     * Get the destination city for the trip.
     */
    public function toCity(): BelongsTo
    {
        return $this->belongsTo(City::class, 'to_city_id');
    }

    /**
     * Get the trip cities for the trip.
     */
    public function tripCities(): HasMany
    {
        return $this->hasMany(TripCity::class);
    }
}
