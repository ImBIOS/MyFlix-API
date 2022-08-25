<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('v1')->group(function () {
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'fetch']);
            Route::put('/', [UserController::class, 'updateProfile']);
            Route::post('photo', [UserController::class, 'updatePhoto']);
        });

        Route::get('movie/{movieId}', [MovieController::class, 'index']);
        Route::get('movies', [MovieController::class, 'getByGenre']);

        Route::get('homepage', [HomeController::class, 'index']);

        Route::post('watchlist', [MovieController::class, 'addWatchlist']);
        Route::get('watchlist', [MovieController::class, 'getWatchlist']);
        Route::delete('watchlist', [MovieController::class, 'removeWatchlist']);

        Route::get('sync', [SyncController::class, 'index']);
        Route::post('logout', [UserController::class, 'logout']);
    });
});

Route::prefix('v1')->group(function () {
    Route::prefix('user')->group(function () {
        Route::post('login', [UserController::class, 'login']);
        Route::post('register', [UserController::class, 'register']);
    });
});
