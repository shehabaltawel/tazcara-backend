<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['user_id', 'seat_id', 'from_trip_city_id', 'to_trip_city_id', 'price', 'status'])]

/**
 * Booking Model
 */
class Booking extends Model
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
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Scope the query to confirmed bookings only.
     */
    #[Scope]
    protected function confirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope the query to bookings made on the given trip.
     */
    #[Scope]
    protected function onTrip(Builder $query, int $tripId): Builder
    {
        return $query->whereHas('fromTripCity', fn ($query) => $query->where('trip_id', $tripId));
    }

    /**
     * Get the user who made the booking.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the seat of the booking.
     */
    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    /**
     * Get the origin stop of the booking.
     */
    public function fromTripCity(): BelongsTo
    {
        return $this->belongsTo(TripCity::class, 'from_trip_city_id');
    }

    /**
     * Get the destination stop of the booking.
     */
    public function toTripCity(): BelongsTo
    {
        return $this->belongsTo(TripCity::class, 'to_trip_city_id');
    }
}
