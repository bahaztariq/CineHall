<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Payment;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::middleware('auth:api')->prefix('transactions')->group(function(){

        Route::post('', [Payment::class, 'createTransaction']);
        Route::get('/prosses', [payment::class, 'processTransaction']);
        Route::get('/succes', [payment::class, 'successTransaction']);
        Route::get('/cancel', [payment::class, 'cancelTransaction']);

}

    );
