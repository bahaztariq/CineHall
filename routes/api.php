<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Payment;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::get('create-transaction', [Payment::class, 'createTransaction'])->name('createTransaction');
Route::get('process-transaction', [payment::class, 'processTransaction'])->name('processTransaction');
Route::get('success-transaction', [payment::class, 'successTransaction'])->name('successTransaction');
Route::get('cancel-transaction', [payment::class, 'cancelTransaction'])->name('cancelTransaction');
