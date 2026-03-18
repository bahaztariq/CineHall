<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function () {

    Route::get('/reservation', [ReservationController::class, 'index'])->name('reservations');
    Route::get('/reservation/{id}', [ReservationController::class, 'show'])->name('reservations');
    Route::post('/reservation', [ReservationController::class, 'store'])->name('reservations');
    Route::put('/reservation/{id}', [ReservationController::class, 'update'])->name('reservations');
    Route::delete('/reservation/{id}', [ReservationController::class, 'delete'])->name('reservations');
});

Route::prefix('profile')->middleware('auth:api')->group(function () {
    Route::get('', [ProfileController::class, 'me']);
    Route::post('', [ProfileController::class, 'update']);
    Route::delete('', [ProfileController::class, 'delete']);
});

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
    });
});
