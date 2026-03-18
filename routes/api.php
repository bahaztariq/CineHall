<?php

use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\FilmController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\AdminController;

Route::prefix('v1')->group(function () {

    // --- PUBLIC ROUTES ---
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    // Public Films
    Route::get('/films', [FilmController::class, 'index']);
    Route::get('/films/search', [FilmController::class, 'search']);
    Route::get('/films/filter', [FilmController::class, 'filter']);
    Route::get('/films/{film}', [FilmController::class, 'show']);

    // Public Sessions & Rooms
    Route::get('/sessions', [SessionController::class, 'index']);
    Route::get('/sessions/{film_session}', [SessionController::class, 'show']);
    Route::get('/rooms', [RoomController::class, 'index']);
    Route::get('/rooms/{room}', [RoomController::class, 'show']);

    // --- PROTECTED ROUTES (JWT) ---
    Route::middleware('auth:api')->group(function () {

        // Auth management
        Route::post('logout', [AuthController::class, 'logout']);
        Route::post('refresh', [AuthController::class, 'refresh']);

        // Profile management
        Route::prefix('profile')->group(function () {
            Route::get('', [ProfileController::class, 'me']); // Using AuthController for consistency
            Route::post('', [ProfileController::class, 'update']);
            Route::delete('', [ProfileController::class, 'delete']);
        });

        // Admin functionality
        Route::get('/statistics', [AdminController::class, 'statistics']);
        Route::patch('/users/{user}/toggle-status', [AdminController::class, 'toggleUserStatus']);

        // Sessions management (Admin-only logic is handled in Controller/Requests)
        Route::post('/sessions', [SessionController::class, 'store']);
        Route::put('/sessions/{film_session}', [SessionController::class, 'update']);
        Route::delete('/sessions/{film_session}', [SessionController::class, 'destroy']);

        // Rooms management (Admin-only logic handled in Controller/Requests)
        Route::post('/rooms', [RoomController::class, 'store']);
        Route::put('/rooms/{room}', [RoomController::class, 'update']);
        Route::delete('/rooms/{room}', [RoomController::class, 'destroy']);

        // Films management
        Route::post('/films', [FilmController::class, 'store']);
        Route::match(['put', 'patch'], '/films/{film}', [FilmController::class, 'update']);
        Route::delete('/films/{film}', [FilmController::class, 'destroy']);

        // Reservations
        Route::apiResource('reservation', ReservationController::class)->names([
            'index'   => 'reservations.index',
            'show'    => 'reservations.show',
            'store'   => 'reservations.store',
            'update'  => 'reservations.update',
            'destroy' => 'reservations.destroy',
        ]);

        // Tickets
        Route::get('/tickets/{ticketId}/download', [TicketController::class, 'donwloadReceipt']);

        // Transactions (PayPal & Stripe)
        Route::prefix('transactions')->group(function () {
            // PayPal
            Route::post('/paypal', [PayPalController::class, 'createTransaction']);
            Route::get('/paypal/success', [PayPalController::class, 'successTransaction'])->name('successTransaction');
            Route::get('/paypal/cancel', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');

            // Stripe
            Route::post('/stripe', [StripeController::class, 'createSession']);
            Route::get('/stripe/success', [StripeController::class, 'handleSuccess'])->name('stripe.success');
            Route::get('/stripe/cancel', [StripeController::class, 'handleCancel'])->name('stripe.cancel');
        });
    });
});
