<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ReservationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmController;

Route::get('/films/search', [FilmController::class, 'search']);
Route::get('/films/filter', [FilmController::class, 'filter']);
Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{film}', [FilmController::class, 'show']);

Route::middleware('auth:api')->group(function () {

    Route::post('/films', [FilmController::class, 'store']);
    Route::match(['put', 'patch'], '/films/{film}', [FilmController::class, 'update']);
    Route::delete('/films/{film}', [FilmController::class, 'destroy']);

});

Route::middleware('auth:api')->prefix('transactions')->group(function () {
    Route::post('', [PayPalController::class, 'createTransaction']);
    Route::get('/success', [PayPalController::class, 'successTransaction'])->name('successTransaction');
    Route::get('/cancel', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

    });


    
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::get('/tickets/{ticketId}/download', [TicketController::class, 'downloadReceipt']); // Corrected 'donwload' to 'download' and 'donwloadReceipt' to 'downloadReceipt'

Route::middleware('auth:api')->group(function(){

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
