<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RoomController;

Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{film}', [FilmController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/films', [FilmController::class, 'store']);
    Route::match(['put', 'patch'], '/films/{film}', [FilmController::class, 'update']);
    Route::delete('/films/{film}', [FilmController::class, 'destroy']);
    
});

Route::get('/sessions', [SessionController::class, 'index']);
Route::get('/sessions/{film_session}', [SessionController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/sessions', [SessionController::class, 'store']);
    Route::put('/sessions/{film_session}', [SessionController::class, 'update']);
    Route::delete('/sessions/{film_session}', [SessionController::class, 'destroy']);
});

Route::get('/rooms', [RoomController::class, 'index']);
Route::get('/rooms/{room}', [RoomController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/rooms', [RoomController::class, 'store']);
    Route::put('/rooms/{room}', [RoomController::class, 'update']);
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::middleware('auth:sanctum')->group(function(){

     Route::get('/reservation',[ReservationController::class, 'index'])->name('reservations');
     Route::get('/reservation/{id}',[ReservationController::class, 'show'])->name('reservations');
     Route::post('/reservation',[ReservationController::class, 'store'])->name('reservations');
     Route::put('/reservation/{id}',[ReservationController::class, 'update'])->name('reservations');
     Route::delete('/reservation/{id}',[ReservationController::class, 'delete'])->name('reservations');

});
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function() {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
    });
});
