<?php

namespace App\Services;

use App\Models\City;
use App\Models\TripCity;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;

/**
 * Admin City Service
 */
class AdminCityService
{
    /**
     * Create a new city.
     */
    public function create(array $data): City
    {
        return City::create($data);
    }

    /**
     * Soft delete the given city, refusing to remove one still used by a trip.
     *
     * @throws ConflictHttpException
     */
    public function delete(City $city): void
    {
        throw_if(
            TripCity::where('city_id', $city->id)->exists(),
            ConflictHttpException::class,
            'Cannot delete a city that is part of a trip'
        );

        $city->delete();
    }
}
