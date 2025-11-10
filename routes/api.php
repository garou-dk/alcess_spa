<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppConfigurationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeaturedImageController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\IslandGroupController;
use App\Http\Controllers\MunicityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('categories', [CategoryController::class, 'index']);

Route::get('best-selling', [ProductController::class, 'bestSelling']);

Route::get('find-product/{id}', [ProductController::class, 'fetchAvailableProduct']);

Route::get('island-groups', [IslandGroupController::class, 'index']);

Route::get('regions/{id}', [RegionController::class, 'index']);

Route::get('provinces/{id}', [ProvinceController::class, 'index']);

Route::get('municities/{id}', [MunicityController::class, 'index']);

Route::get('barangays/{id}', [BarangayController::class, 'index']);

Route::controller(ProductController::class)
    ->prefix('products')
    ->group(function () {
        Route::get('/', 'searchProduct');
    });

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
                    Route::post('/{id}', 'store');
                    Route::delete('/{id}', 'destroy');
                    Route::patch('/{id}', 'update');
                });

            Route::controller(FeaturedImageController::class)
                ->prefix('featured-images')
                ->group(function () {
                    Route::get('/{id}', 'index');
                    Route::post('/{id}', 'store');
                    Route::delete('/{id}', 'destroy');
                });

            Route::prefix('results')
                ->group(function () {
                    Route::get('low-stock', [ProductController::class, 'countLowStock']);
                    Route::get('active-product', [ProductController::class, 'activeProductCount']);
                    Route::get('out-of-stock', [ProductController::class, 'outOfStockCount']);
                });

            Route::controller(OrderController::class)
                ->prefix('orders')
                ->group(function () {
                    Route::get('/', 'getAllOrders');
                    Route::patch('/approval/{id}', 'approveOrDecline');
                    Route::patch('/cancel/{id}', 'cancelOrder');
                    Route::get('/payment-image/{id}', 'fetchPaymentImage');
                    Route::patch('/confirm-payment/{id}', 'confirmPayment');
                    Route::patch('/for-delivery/{id}', 'setToShipped');
                });

            Route::controller(AppConfigurationController::class)
                ->prefix('app-configurations')
                ->group(function () {
                    Route::get('/online-bank', 'getOnlineBank');
                    Route::post('/online-bank', 'setBankAccount');
                    Route::get('bank-screenshot', 'getBankAccountScreenshot');
                });

            Route::controller(SaleController::class)
                ->prefix('sales')
                ->group(function () {
                    Route::post('record', 'recordSales');
                });
        });
    });

    Route::prefix('customer')
        ->middleware(['role:'.RoleEnum::CUSTOMER->value])
        ->group(function () {
            Route::controller(AppConfigurationController::class)
                ->group(function () {
                    Route::get('owner-online-bank', 'getOnlineBank');
                    Route::get('bank-screenshot', 'getBankAccountScreenshot');
                });

            Route::controller(CartController::class)
                ->prefix('carts')
                ->group(function () {
                    Route::get('/', 'index');
                    Route::post('/', 'store');
                    Route::get('/count', 'cartCount');
                    Route::delete('/remove/multiple', 'removeMultiple');
                    Route::delete('/{id}', 'remove');
                });

            Route::controller(AddressController::class)
                ->prefix('addresses')
                ->group(function () {
                    Route::get('/{id}', 'find');
                    Route::post('/', 'save');
                });

            Route::controller(OrderController::class)
                ->prefix('orders')
                ->group(function () {
                    Route::post('/', 'orderProducts');
                    Route::get('/{id}', 'getOrder');
                    Route::get('/', 'getCustomerOrders');
                    Route::patch('/cancel/{id}', 'cancelOrder');
                    Route::patch('/set-payment/{id}', 'setPayment');
                    Route::patch('/mark-delivered/{id}', 'markAsReceived');
                    Route::patch('confirm-cash-delivery/{id}', 'cashOnDeliveryConfirm');
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
