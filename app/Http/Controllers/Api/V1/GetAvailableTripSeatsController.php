<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\V1\GetAvailableTripSeatsRequest;
use App\Http\Resources\Api\V1\TripSeatsResource;
use App\Services\TripService;
use Illuminate\Http\JsonResponse;

/**
 * Get Available Trip Seats Controller
 */
class GetAvailableTripSeatsController extends BaseController
{
    public function __construct(private readonly TripService $tripService) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(GetAvailableTripSeatsRequest $request): JsonResponse
    {
        return $this->jsonSuccess(
            TripSeatsResource::collection(
                $this->tripService->getAvailableTripSeats($request->validated())
            ),
            'Available trip seats'
        );
    }
}
