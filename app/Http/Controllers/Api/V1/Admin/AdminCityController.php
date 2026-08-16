<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\V1\Admin\StoreCityRequest;
use App\Http\Resources\Api\V1\Admin\CityResource;
use App\Models\City;
use App\Services\AdminCityService;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admin City Controller
 */
class AdminCityController extends BaseController
{
    public function __construct(private readonly AdminCityService $cityService) {}

    /**
     * List all cities.
     */
    public function index(): JsonResponse
    {
        return $this->jsonSuccess(CityResource::collection(City::orderBy('name')->get()));
    }

    /**
     * Create a new city.
     */
    public function store(StoreCityRequest $request): JsonResponse
    {
        return $this->jsonSuccess(
            CityResource::make($this->cityService->create($request->validated())),
            'City created successfully',
            Response::HTTP_CREATED
        );
    }

    /**
     * Soft delete the given city.
     */
    public function destroy(City $city): Response
    {
        $this->cityService->delete($city);

        return response()->noContent();
    }
}
