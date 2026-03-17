<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItineraryController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\FilmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public Routes
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/register', [AuthController::class, 'register']);

    Route::get('/films', [FilmController::class, 'index']);
    Route::get('/films/{film}', [FilmController::class, 'show']);

    Route::get('/tickets/{ticketId}/donwload', [TicketController::class, 'donwloadReceipt']);

    // Protected Routes (JWT)
    Route::middleware('auth:api')->group(function () {

        // Auth management
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);
        Route::get('me', [AuthController::class, 'me']);
        

        // Current User
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        // Films management
        Route::post('/films', [FilmController::class, 'store']);
        Route::match(['put', 'patch'], '/films/{film}', [FilmController::class, 'update']);
        Route::delete('/films/{film}', [FilmController::class, 'destroy']);

        // Reservations
        Route::apiResource('reservation', ReservationController::class)->names([
            'index' => 'reservations.index',
            'show' => 'reservations.show',
            'store' => 'reservations.store',
            'update' => 'reservations.update',
            'destroy' => 'reservations.delete',
        ]);

        // Transactions
        Route::post('transactions', [PayPalController::class, 'createTransaction']);
        Route::get('transactions/success', [PayPalController::class, 'successTransaction'])->name('successTransaction');
        Route::get('transactions/cancel', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');
    });

});
