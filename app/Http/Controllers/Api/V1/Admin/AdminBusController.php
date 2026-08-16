<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\V1\Admin\StoreBusRequest;
use App\Http\Resources\Api\V1\Admin\BusResource;
use App\Models\Bus;
use App\Services\AdminBusService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin Bus Controller
 */
class AdminBusController extends BaseController
{
    public function __construct(private readonly AdminBusService $busService) {}

    /**
     * List all buses with their seats.
     */
    public function index(): JsonResponse
    {
        return $this->jsonSuccess(
            BusResource::collection(Bus::with('seats')->orderBy('plate_number')->get())
        );
    }

    /**
     * Create a new bus and optionally its seats.
     */
    public function store(StoreBusRequest $request): JsonResponse
    {
        return $this->jsonSuccess(
            BusResource::make($this->busService->create($request->validated())),
            'Bus created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Soft delete the given bus.
     */
    public function destroy(Bus $bus): Response
    {
        $this->busService->delete($bus);

        return response()->noContent();
    }
}
