<?php

use App\Http\Controllers\Api\V1\GetAvailableTripSeatsController;
use App\Http\Controllers\Api\V1\StoreTripBookingController;
use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::get('trips/available-seats', GetAvailableTripSeatsController::class)->middleware('auth:sanctum');
    Route::post('trips/{trip}/book-seats', StoreTripBookingController::class)->middleware('auth:sanctum');
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
