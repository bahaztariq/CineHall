<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FilmController;

Route::get('/films', [FilmController::class, 'index']);
Route::get('/films/{film}', [FilmController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    
    Route::post('/films', [FilmController::class, 'store']);
    Route::match(['put', 'patch'], '/films/{film}', [FilmController::class, 'update']);
    Route::delete('/films/{film}', [FilmController::class, 'destroy']);
    
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
