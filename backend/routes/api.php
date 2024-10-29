<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DriverController;

Route::post('/login', LoginController::class);
Route::post('/login/verify', [LoginController::class, 'verify']);

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/drivers', [DriverController::class, 'show']);
    Route::post('/drivers', [DriverController::class, 'update']);

    Route::get('/users', function(Request $request) {
        return $request->user();
    });
});
