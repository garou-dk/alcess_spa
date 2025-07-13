<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeaturedImageController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('check', [AuthController::class, 'checkAuth']);
    Route::post('logout', [AuthController::class, 'logoutUser']);
    Route::prefix('admin')->group(function () {
        Route::middleware(['role:'.RoleEnum::ADMIN->value.'-'.RoleEnum::STAFF->value])->group(function () {
            Route::get('roles', [RoleController::class, 'index']);
            Route::controller(UserController::class)
                ->prefix('users')->group(function () {
                    Route::get('/', 'index');
                    Route::post('/', 'createUser');
                    Route::patch('/change-name/{id}', 'changeName');
                    Route::patch('/change-password/{id}', 'changePassword');
                    Route::patch('/change-profile/{id}', 'changeProfile');
                    Route::patch('/change-role/{id}', 'changeRole');
                    Route::patch('/change-status/{id}', 'changeStatus');
                });

            Route::controller(UnitController::class)
                ->prefix('units')
                ->group(function () {
                    Route::get('/', 'fetchAll');
                    Route::post('/', 'store');
                    Route::patch('/{id}', 'update');
                    Route::delete('/{id}', 'destroy');
                    Route::patch('/recover/{id}', 'recover');
                });

            Route::controller(CategoryController::class)
                ->prefix('categories')
                ->group(function () {
                    Route::get('/', 'index');
                    Route::post('/', 'store');
                    Route::delete('/{id}', 'destroy');
                    Route::patch('/update-name/{id}', 'updateCategoryName');
                    Route::patch('/recover/{id}', 'restore');
                    Route::patch('/update-image/{id}', 'updateCategoryImage');
                });

            Route::controller(ProductController::class)
                ->prefix('products')
                ->group(function () {
                    Route::get('/', 'index');
                    Route::post('/', 'store');
                    Route::delete('/{id}', 'destroy');
                    Route::patch('/{id}', 'update');
                    Route::patch('/update-image/{id}', 'changeImage');
                    Route::patch('/change-status/{id}', 'changeStatus');
                });

            Route::controller(SpecificationController::class)
                ->prefix('specifications')
                ->group(function () {
                    Route::get('/{id}', 'index');
                    Route::post('/', 'store');
                    Route::delete('/{id}', 'destroy');
                    Route::patch('/{id}', 'update');
                });

            Route::controller(FeaturedImageController::class)
                ->prefix('featured-images')
                ->group(function () {
                    Route::get('/{id}', 'index');
                    Route::post('/', 'store');
                    Route::delete('/{id}', 'destroy');
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
