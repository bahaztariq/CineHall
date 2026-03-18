<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\FilmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StripeController;


Route::prefix('v1')->group(function () {

    // Auth Public
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Public Films
    Route::get('/films/search', [FilmController::class, 'search']);
    Route::get('/films/filter', [FilmController::class, 'filter']);
    Route::get('/films', [FilmController::class, 'index']);
    Route::get('/films/{film}', [FilmController::class, 'show']);

    // Public Sessions & Rooms
    Route::get('/sessions', [SessionController::class, 'index']);
    Route::get('/sessions/{film_session}', [SessionController::class, 'show']);
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/{room}', [RoomController::class, 'show']);

    // Tickets
    Route::get('/tickets/{ticketId}/download', [TicketController::class, 'downloadReceipt']);

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

        // Film Sessions management
        Route::post('/sessions', [SessionController::class, 'store']);
        Route::put('/sessions/{film_session}', [SessionController::class, 'update']);
        Route::delete('/sessions/{film_session}', [SessionController::class, 'destroy']);

        // Rooms management
        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{room}', [RoomController::class, 'update']);
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

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

        // Transactions (PayPal/Payments)
        Route::prefix('transactions')->group(function () {
            Route::post('', [PayPalController::class, 'createTransaction']);
            Route::get('/success', [PayPalController::class, 'successTransaction'])->name('successTransaction');
            Route::get('/cancel', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

            // Stripe
            Route::post('/stripe', [StripeController::class, 'createSession']);
            Route::get('/stripe/success', [StripeController::class, 'handleSuccess'])->name('stripe.success');
            Route::get('/stripe/cancel', [StripeController::class, 'handleCancel'])->name('stripe.cancel');

        });
    });
});
