<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\V1\StoreBookingRequest;
use App\Http\Resources\Api\V1\BookingResource;
use App\Models\Trip;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Store Trip Booking Controller
 */
class StoreTripBookingController extends BaseController
{
    public function __construct(private readonly BookingService $bookingService) {}

    /**
     * Handle the incoming request.
     */
    public function __invoke(StoreBookingRequest $request, Trip $trip): JsonResponse
    {
        $bookings = $this->bookingService->bookMany(
            $trip,
            $request->user(),
            $request->validated(),
            $request->header('Idempotency-Key')
        );

        return $this->jsonSuccess(
            BookingResource::collection($bookings),
            'Booking confirmed',
            Response::HTTP_CREATED
        );
    }
}
