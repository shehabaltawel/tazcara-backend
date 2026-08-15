<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['bus_id', 'code'])]

/**
 * Seat Model
 */
class Seat extends Model
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
     * Scope the query to seats belonging to the given bus.
     */
    #[Scope]
    protected function forBus(Builder $query, int $busId): Builder
    {
        return $query->where('bus_id', $busId);
    }

    /**
     * Get the bus for the seat.
     */
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }
}
