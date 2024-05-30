<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Route::controller(AnimeController::class)->group(function () {
//     Route::post('anime', 'store');
//     Route::get('anime', 'index');
//     Route::get('anime/{id}', 'getById');
//     Route::put('anime/{id}', 'update');
//     Route::delete('anime/{id}', 'delete');
// });