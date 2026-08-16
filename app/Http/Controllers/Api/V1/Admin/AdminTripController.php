<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\V1\Admin\StoreTripRequest;
use App\Http\Resources\Api\V1\Admin\TripResource;
use App\Models\Trip;
use App\Services\AdminTripService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Trip Controller
 */
class AdminTripController extends BaseController
{
    public function __construct(private readonly AdminTripService $adminTripService) {}

    /**
     * List all trips with their stops.
     */
    public function index(): JsonResponse
    {
        return $this->jsonSuccess(
            TripResource::collection(
                Trip::with(['bus', 'fromCity', 'toCity', 'tripCities.city'])
                    ->orderBy('departure_timestamp')
                    ->get()
            )
        );
    }

    /**
     * Create a trip with its ordered stops.
     */
    public function store(StoreTripRequest $request): JsonResponse
    {
        return $this->jsonSuccess(
            TripResource::make($this->adminTripService->create($request->validated())),
            'Trip created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Soft delete the given trip.
     */
    public function destroy(Trip $trip): Response
    {
        $this->adminTripService->delete($trip);

        return response()->noContent();
    }
}
