<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\stripeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\FilmController;
use App\Http\Controllers\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{film}', [FilmController::class, 'show']);

Route::middleware('auth:api')->group(function () {
    Route::post('/films', [FilmController::class, 'store']);
    Route::match(['put', 'patch'], '/films/{film}', [FilmController::class, 'update']);
    Route::delete('/films/{film}', [FilmController::class, 'destroy']);
});

Route::middleware('auth:api')->prefix('transactions')->group(function () {
    // PayPal
    Route::post('/paypal', [PayPalController::class, 'createTransaction']);
    Route::get('/paypal/success', [PayPalController::class, 'successTransaction'])->name('successTransaction');
    Route::get('/paypal/cancel', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

    // Stripe
    Route::post('/stripe', [stripeController::class, 'createSession']);
    Route::get('/stripe/success', [stripeController::class, 'handleSuccess'])->name('stripe.success');
    Route::get('/stripe/cancel', [stripeController::class, 'handleCancel'])->name('stripe.cancel');
});


    
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');


Route::get('/tickets/{ticketId}/donwload', [TicketController::class, 'donwloadReceipt']);

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
