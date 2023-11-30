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
    // Rutas para auth
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register'])->withoutMiddleware(['auth:api']);
        Route::post('login', [AuthController::class, 'login'])->withoutMiddleware(['auth:api']);
        Route::get('me', [AuthController::class, 'details'])->withoutMiddleware(['auth:api']);
    });

    Route::middleware(['jwt.verify'])->group(function () {
    // Rutas para accounts
    Route::prefix('accounts')->group(function () {
        Route::get('balance', [AccountController::class, 'showBalance']);
        Route::get('{id}', [AccountController::class, 'show']);
        Route::post('/', [AccountController::class, 'store']);
        Route::patch('{id}', [AccountController::class, 'editTransactionLimit']);
    });

    // Rutas para users
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index']);
        Route::delete('{id}', [UserController::class, 'destroy']);
    });
      Route::post('auth/me', [UserController::class, 'update']);

    // Rutas para fixed_terms
    Route::prefix('fixed_terms')->group(function () {
        Route::post('/', [FixedTermController::class, 'store']);
        Route::post('/simulate', [FixedTermController::class, 'simulateFixedTerm']);
    });


    // Rutas para transactions
    Route::prefix('transactions')->group(function () {
        Route::post('send', [TransactionController::class, 'sendMoney']);
        Route::post('deposit', [TransactionController::class, 'depositMoney']);
        Route::post('payment', [TransactionController::class, 'makePayment']);
        Route::patch('{id}', [TransactionController::class, 'edit']);
        Route::get('/', [TransactionController::class, 'listTransactions']);
        Route::get('/{id}', [TransactionController::class, 'showTransaction']);
    });
});
});