<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\BaseController;
use App\Http\Requests\Api\V1\LoginRequest;
use App\Http\Requests\Api\V1\RegisterRequest;
use App\Http\Resources\Api\V1\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Auth Controller
 */
class AuthController extends BaseController
{
    public function __construct(private readonly AuthService $authService) {}

    public function login(LoginRequest $request): JsonResponse
    {
        return $this->jsonSuccess(
            AuthResource::make($this->authService->login($request->validated())),
            'Login successful'
        );
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        return $this->jsonSuccess(
            AuthResource::make($this->authService->register($request->validated())),
            'Registration successful'
        );
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->jsonSuccess([], 'Logged out successfully');
    }
}
