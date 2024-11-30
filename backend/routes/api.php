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
  
    Route::get('/users', function(Request $request) {
        return $request->user();
    });

    Route::get('/user/driver', [DriverController::class, 'getUserWithDriver']);
    Route::put('/user/driver', [DriverController::class, 'updateUserWithDriver']);
    Route::get('/user/edit', [UserController::class, 'edit']);

    Route::post('/driver/status', [DriverController::class, 'updateStatus']);
    Route::get('/driver/status', [DriverController::class, 'getStatus']);

    Route::post('/rides/search-drivers', [RideController::class, 'searchDrivers']);
    Route::post('/rides', [RideController::class, 'store']);
    Route::get('/rides/{ride}', [RideController::class, 'show']);
    Route::put('/rides/{ride}/accept', [RideController::class, 'accept']);
    Route::put('/rides/{ride}/start', [RideController::class, 'start']);
    Route::put('/rides/{ride}/complete', [RideController::class, 'complete']);
    Route::put('/rides/{ride}/driver-location', [RideController::class, 'updateDriverLocation']);
    Route::post('/rides/{ride}/cancel', [RideController::class, 'cancel']);
    Route::get('/rides/{ride}/track', [RideController::class, 'track']);
});