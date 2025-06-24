<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('check', [AuthController::class, 'checkAuth']);
    Route::post('logout', [AuthController::class, 'logoutUser']);

});

Route::middleware(['guest-only'])
    ->group(function () {
        Route::post('login', [AuthController::class, 'authenticateCustomer']);
        Route::post('register', [UserController::class, 'registerCustomer']);
        Route::prefix('admin')->group(function () {
            Route::post('login', [AuthController::class, 'authenticateAdmin']);
        });
        Route::post('verify/{uuid}', [UserController::class, 'verifyEmail'])->middleware('signed')->name('verify-email');
    });
