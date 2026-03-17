<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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