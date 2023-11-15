<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['api', 'auth:api'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->name('auth.registro')->withoutMiddleware(['auth:api']);
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['auth:api']);
    });

    Route::apiResource('accounts', AccountController::class);
    Route::apiResource('users', UserController::class);
});
