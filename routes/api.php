<?php

use App\Http\Controllers\Api\V1\Admin\AdminBusController;
use App\Http\Controllers\Api\V1\Admin\AdminCityController;
use App\Http\Controllers\Api\V1\Admin\AdminTripController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\GetAvailableTripSeatsController;
use App\Http\Controllers\Api\V1\StoreTripBookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->middleware('throttle:6,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });

    Route::prefix('trips')->middleware('auth:sanctum')->group(function () {
        Route::get('available-seats', GetAvailableTripSeatsController::class);
        Route::post('{trip}/book-seats', StoreTripBookingController::class);
    });

    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::apiResource('trips', AdminTripController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('cities', AdminCityController::class)->only(['index', 'store', 'destroy']);
        Route::apiResource('buses', AdminBusController::class)->only(['index', 'store', 'destroy']);
    });
});
