<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\RouteKey;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable(['class', 'plate_number'])]
#[RouteKey('uuid')]

/**
 * Bus Model
 */
class Bus extends Model
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
     * Get the seats for the bus.
     */
    public function seats()
    {
        return $this->hasMany(Seat::class);
    }
}
