<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\V1\StoreBookingRequest;
use App\Http\Resources\Api\V1\BookingResource;
use App\Models\Trip;
use App\Services\BookingService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use InvalidArgumentException;
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
        try {
            $bookings = $this->bookingService->bookMany($trip, $request->user(), $request->validated());

            return $this->jsonSuccess(
                BookingResource::collection($bookings),
                'Booking confirmed',
                Response::HTTP_CREATED
            );
        } catch (InvalidArgumentException $exception) {
            return $this->jsonError($exception->getMessage(), Response::HTTP_BAD_REQUEST);
        } catch (ModelNotFoundException) {
            return $this->jsonError('Resource not found', Response::HTTP_NOT_FOUND);
        } catch (Exception $exception) {
            return $this->jsonError($exception->getMessage(), Response::HTTP_CONFLICT);
        }
    }
}
