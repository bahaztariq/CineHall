<?php

use App\Http\Controllers\PayPalController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->prefix('transactions')->group(function () {
    Route::post('', [PayPalController::class, 'createTransaction']);
    Route::get('/success', [PayPalController::class, 'successTransaction'])->name('successTransaction');
    Route::get('/cancel', [PayPalController::class, 'cancelTransaction'])->name('cancelTransaction');
});
