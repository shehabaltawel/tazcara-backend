<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\GetAvailableTripSeatsController;
use App\Http\Controllers\Api\V1\StoreTripBookingController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->middleware('throttle:6,1')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    Route::prefix('trips')->middleware('auth:sanctum')->group(function () {
        Route::get('available-seats', GetAvailableTripSeatsController::class);
        Route::post('{trip}/book-seats', StoreTripBookingController::class);
    });
});
