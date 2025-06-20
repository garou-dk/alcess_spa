<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['guest-only'])
    ->group(function () {
        Route::post('login', [AuthController::class, 'authenticateCustomer']);
        Route::prefix('admin')->group(function () {
            Route::post('login', [AuthController::class, 'authenticateAdmin']);
        });
    });

Route::get('check', [AuthController::class, 'checkAuth']);
