<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('check', [AuthController::class, 'checkAuth']);
    Route::post('logout', [AuthController::class, 'logoutUser']);
    Route::prefix('admin')->group(function () {
        Route::middleware(['role:'.RoleEnum::ADMIN->value.','.RoleEnum::STAFF->value])->group(function () {
            Route::get('roles', [RoleController::class, 'index']);
            Route::controller(UserController::class)
                ->prefix('users')->group(function () {
                    Route::get('/', 'index');
                    Route::post('/', 'createUser');
                    Route::patch('/change-name/{id}', 'changeName');
                    Route::patch('/change-password/{id}', 'changePassword');
                    // Route::get('/{id}', 'fetchUser');
                    // Route::post('/', 'createUser');
                    // Route::put('/{id}', 'updateUser');
                    // Route::delete('/{id}', 'deleteUser');
                });
        });
    });

    Route::controller(FileController::class)
        ->group(function () {
            Route::get('profile/{image}', 'fetchProfile');
        });
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
