<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FixedTermController;
use App\Http\Controllers\TransactionController;
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

    // Rutas para accounts
    Route::get('accounts/balance', [AccountController::class, 'showBalance']);
    Route::get('accounts/{id}', [AccountController::class, 'show']);
    Route::post('/accounts', [AccountController::class,'store']);

    // Rutas para users
    Route::get('users', [UserController::class, 'index']);
    Route::delete('users/{id}', [UserController::class, 'destroy']);

    Route::post('fixed_terms', [FixedTermController::class, 'store']);

    // Rutas para transactions
    Route::prefix('transactions')->group(function () {
        Route::post('send', [TransactionController::class, 'sendMoney']);
        Route::post('deposit', [TransactionController::class, 'depositMoney']);
        Route::post('payment', [TransactionController::class, 'makePayment']);
        Route::get('/', [TransactionController::class, 'listTransactions']);
        Route::get('/{id}', [TransactionController::class, 'showTransaction']);
        
    });
});