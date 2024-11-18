<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\RideController;

Route::post('/login', LoginController::class);
Route::post('/login/register', [LoginController::class, 'register']);
Route::post('/login/verify', [LoginController::class, 'verify']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/drivers', [DriverController::class, 'show']);
    Route::post('/drivers', [DriverController::class, 'store']);

    Route::post('/trip', [TripController::class, 'store']);
    Route::get('/trip/{trip}', [TripController::class, 'show']);
    Route::put('/trip/{trip}/accept', [TripController::class, 'accept']);
    Route::put('/trip/{trip}/start', [TripController::class, 'start']);
    Route::put('/trip/{trip}/complete', [TripController::class, 'complete']);
    Route::put('/trip/{trip}/driver-location', [TripController::class, 'updateDriverLocation']);

    Route::get('/users', function(Request $request) {
        return $request->user();
    });

    Route::get('/user/driver', [DriverController::class, 'getUserWithDriver']);
    Route::put('/user/driver', [DriverController::class, 'updateUserWithDriver']);
    Route::get('/user/edit', [UserController::class, 'edit']);

    Route::post('/driver/status', [DriverController::class, 'updateStatus']);
    Route::get('/driver/status', [DriverController::class, 'getStatus']);

    Route::post('/rides/search-drivers', [RideController::class, 'searchDrivers']);
});