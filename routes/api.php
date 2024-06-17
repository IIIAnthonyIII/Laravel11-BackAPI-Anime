<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->middleware('auth:api')->name('me');
    Route::put('/user/{id}', [AuthController::class, 'update'])->middleware('auth:api')->name('update');
    Route::get('/user/{id}', [AuthController::class, 'getById'])->middleware('auth:api')->name('getById');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router) {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::delete('/{id}', [UserController::class, 'delete'])->name('delete');
    Route::put('/activar/{id}', [UserController::class, 'activar'])->name('activar');
});

// Route::controller(AnimeController::class)->group(function () {
//     Route::post('anime', 'store');
//     Route::get('anime', 'index');
//     Route::get('anime/{id}', 'getById');
//     Route::put('anime/{id}', 'update');
//     Route::delete('anime/{id}', 'delete');
// });