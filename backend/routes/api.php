<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\RideController;

Route::post('/login', LoginController::class);
Route::post('/login/register', [LoginController::class, 'register']);
Route::post('/login/verify', [LoginController::class, 'verify']);

Route::group(['middleware' => 'auth:sanctum'], function () {
  
    Route::post('/ride', [rideController::class, 'store']);
    Route::get('/ride/{ride}', [rideController::class, 'show']);
    Route::put('/ride/{ride}/accept', [rideController::class, 'accept']);
    Route::put('/ride/{ride}/start', [rideController::class, 'start']);
    Route::put('/ride/{ride}/complete', [rideController::class, 'complete']);
    Route::put('/ride/{ride}/driver-location', [rideController::class, 'updateDriverLocation']);

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