<?php
use App\Http\Controllers\AnimeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login',    [AuthController::class, 'login'])->name('login');
    Route::post('/logout',   [AuthController::class, 'logout'])->middleware('auth:api')->name('logout');
    Route::post('/refresh',  [AuthController::class, 'refresh'])->middleware('auth:api')->name('refresh');
    Route::post('/me',       [AuthController::class, 'me'])->middleware('auth:api')->name('me');
    Route::put('/user/{id}', [AuthController::class, 'update'])->middleware('auth:api')->name('update');
    Route::get('/user/{id}', [AuthController::class, 'getById'])->middleware('auth:api')->name('getById');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router) {
    Route::get('/',             [UserController::class, 'index'])->name('index');
    Route::delete('/{id}',      [UserController::class, 'delete'])->name('delete');
    Route::put('/activar/{id}', [UserController::class, 'activar'])->name('activar');
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'anime'
], function ($router) {
    Route::get('/',             [AnimeController::class, 'index'])->name('index');
    Route::get('/{id}',         [AnimeController::class, 'getById'])->name('getById');
    Route::post('/',            [AnimeController::class, 'store'])->middleware('auth:api')->name('store');
    Route::put('/{id}',         [AnimeController::class, 'update'])->middleware('auth:api')->name('update');
    Route::post('/{id}',        [AnimeController::class, 'delete'])->middleware('auth:api')->name('delete');
    Route::put('/activar/{id}', [AnimeController::class, 'activar'])->middleware('auth:api')->name('activar');
});
