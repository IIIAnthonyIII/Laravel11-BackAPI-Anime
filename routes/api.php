<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

// Route::controller(AnimeController::class)->group(function () {
//     Route::post('anime', 'store');
//     Route::get('anime', 'index');
//     Route::get('anime/{id}', 'getById');
//     Route::put('anime/{id}', 'update');
//     Route::delete('anime/{id}', 'delete');
// });

Route::controller(UserController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::put('user/{id}', 'update');
    Route::get('user/{id}', 'getById');
    Route::post('user/change-password', 'changePassword');
    Route::post('checkToken', 'checkToken');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('me', 'me');
});

// Route::group([
//     'middleware' => 'api',
//     'prefix' => 'user'
// ], function ($router) {
//     Route::post('/register', [UserController::class, 'register'])->name('register');
//     Route::post('/login', [UserController::class, 'login'])->name('login');
//     Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:api')->name('logout');
//     Route::post('/refresh', [UserController::class, 'refresh'])->middleware('auth:api')->name('refresh');
//     Route::post('/me', [UserController::class, 'me'])->middleware('auth:api')->name('me');
// });