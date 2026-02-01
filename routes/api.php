<?php

use App\Enums\RoleEnum;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\AppConfigurationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarangayController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeaturedImageController;
use App\Http\Controllers\FileController;
use App\Http\Controllers\IslandGroupController;
use App\Http\Controllers\MunicityController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderNotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\RateController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rate Limiting Configuration
|--------------------------------------------------------------------------
*/
RateLimiter::for('auth', function (Request $request) {
    return Limit::perMinute(5)->by($request->ip())->response(function () {
        return response()->json([
            'message' => 'Too many login attempts. Please try again in a minute.',
        ], 429);
    });
});

RateLimiter::for('password-reset', function (Request $request) {
    return Limit::perMinute(3)->by($request->ip())->response(function () {
        return response()->json([
            'message' => 'Too many password reset attempts. Please try again later.',
        ], 429);
    });
});

RateLimiter::for('api', function (Request $request) {
    return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
});

RateLimiter::for('public', function (Request $request) {
    return Limit::perMinute(30)->by($request->ip());
});

/*
|--------------------------------------------------------------------------
| Public Routes (Rate Limited)
|--------------------------------------------------------------------------
*/
Route::middleware(['throttle:public'])->group(function () {
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
            Route::get('/category/{id}','getProductByCategory');
            Route::get('search','searchProductName');
        });
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (API Rate Limited)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::get('check', [AuthController::class, 'checkAuth']);
    Route::post('logout', [AuthController::class, 'logoutUser']);
    Route::prefix('admin')->group(function () {
        // Admin-only routes (user management)
        Route::middleware(['role:'.RoleEnum::ADMIN->value])->group(function () {
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
        });

        // Shared Admin-Staff routes
        Route::middleware(['role:'.RoleEnum::ADMIN->value.'-'.RoleEnum::STAFF->value])->group(function () {
            Route::get('roles', [RoleController::class, 'index']);

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
                    Route::post('/validate-info', 'validateProductInfo');
                    Route::post('/validate-product-name', 'validateProductName');
                    Route::post('/validate-sku', 'validateSku');
                    Route::post('/create-with-media', 'createWithMedia');
                    Route::delete('/{id}', 'destroy');
                    Route::patch('/{id}', 'update');
                    Route::patch('/update-image/{id}', 'changeImage');
                    Route::patch('/change-status/{id}', 'changeStatus');
                    Route::patch('/{id}/add-stock', 'addStock');
                    Route::get('/sku-search/{id}', 'searchBySku');
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
                    Route::patch('/accept/{id}', 'acceptOrder');
                    Route::patch('/decline/{id}', 'declineOrder');
                    Route::patch('/confirm-payment-status/{id}', 'confirmPaymentStatus');
                    Route::patch('/cancel-payment/{id}', 'cancelPayment');
                    Route::get('/payment-image/{id}', 'fetchPaymentImage');
                    Route::post('/set-delivery/{id}', 'setDeliveryWithProof');
                    Route::post('/generate-invoice/{id}', 'generateInvoice');
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
                    Route::get('/', 'getAllSales');
                    Route::post('/', 'recordSale');
                    Route::get('/today-stats', 'getTodayStats');
                    Route::get('/{id}', 'getSale');
                });

            Route::controller(BatchController::class)
                ->prefix('batches')
                ->group(function () {
                    Route::post('/', 'store');
                    Route::get('/', 'recommendedBatch');
                });

            Route::prefix('reports')
                ->group(function () {
                    Route::get('inventory', [ProductController::class, 'inventoryCount']);
                    Route::get('customers', [UserController::class, 'customerList']);
                    Route::get('online-sales', [DashboardController::class, 'getOnlineSalesReport']);
                    Route::get('walkin-sales', [DashboardController::class, 'getWalkInSalesReport']);
                    Route::get('customer-reviews', [DashboardController::class, 'getCustomerReviewReport']);
                });

            Route::controller(DashboardController::class)
                ->prefix('dashboard')
                ->group(function () {
                    Route::get('aggregated', 'getAggregatedDashboard');
                    Route::get('monthly-revenue', 'getMonthlyRevenue');
                    Route::get('pending-orders', 'getPendingOrdersReport');
                    Route::get('inventory-value', 'getTotalInventoryValue');
                    Route::get('top-sellers', 'getTop5MostSoldItems');
                    Route::get('monthly-report', 'getCurrentMonthReport');
                    Route::get('category-sales', 'getRevenueByCategories');
                    Route::get('stock-in-out', 'getStockInOutReport');
                    Route::get('pending', 'getPendingOrders');
                    Route::get('sales-report', 'getRevenueReport');
                    Route::get('delivery-report', 'getForDeliveryReport');
                    Route::get('online-sales-report', 'getOnlineSalesReport');
                    Route::get('walkin-sales-report', 'getWalkInSalesReport');
                    Route::get('customer-review-report', 'getCustomerReviewReport');
                });

            Route::controller(RateController::class)
                ->prefix('rates')
                ->group(function () {
                    Route::get('/', 'fetchRateWithoutComment');
                    Route::patch('/{id}', 'addReply');
                });
            
            Route::controller(OrderNotificationController::class)
                ->prefix('order-notifications')
                ->group(function () {
                    Route::get('/', 'index');
                    Route::patch('/mark-as-read/{id}', 'markAsRead');
                    Route::patch('/mark-all-as-read', 'markAllAsRead');
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
                    Route::get('/paid', 'getPaidOrders');
                    Route::get('/{id}', 'getOrder');
                    Route::get('/', 'getCustomerOrders');
                    Route::patch('/cancel/{id}', 'cancelOrder');
                    Route::patch('/set-payment/{id}', 'setPayment');
                    Route::patch('/mark-delivered/{id}', 'markAsReceived');
                    Route::patch('confirm-cash-delivery/{id}', 'cashOnDeliveryConfirm');
                });

            Route::controller(RateController::class)
                ->prefix('rates')
                ->group(function () {
                    Route::post('/{id}', 'addRate');
                    Route::post('/{id}/like', 'toggleLike');
                });

            Route::controller(OrderNotificationController::class)
                ->prefix('order-notifications')
                ->group(function () {
                    Route::get('/', 'index');
                    Route::patch('/mark-as-read/{id}', 'markAsRead');
                    Route::patch('/mark-all-as-read', 'markAllAsRead');
                });
        });

    Route::controller(FileController::class)
        ->group(function () {
            Route::get('profile/{image}', 'fetchProfile');
        });
});



/*
|--------------------------------------------------------------------------
| Guest Routes (Authentication - Strict Rate Limiting)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest-only'])
    ->group(function () {
        // Login routes with strict rate limiting (5 attempts per minute)
        Route::middleware(['throttle:auth'])->group(function () {
            Route::post('login', [AuthController::class, 'authenticateCustomer']);
            Route::prefix('admin')->group(function () {
                Route::post('login', [AuthController::class, 'authenticateAdmin']);
            });
        });

        Route::post('register', [UserController::class, 'registerCustomer']);
        Route::post('verify/{uuid}', [UserController::class, 'verifyEmail'])->middleware('signed')->name('verify-email');
        
        // Password reset with strict rate limiting (3 attempts per minute)
        Route::prefix('password')->middleware(['throttle:password-reset'])->group(function () {
            Route::post('send-code', [App\Http\Controllers\PasswordResetController::class, 'sendCode']);
            Route::post('verify-code', [App\Http\Controllers\PasswordResetController::class, 'verify']);
            Route::post('reset', [App\Http\Controllers\PasswordResetController::class, 'reset']);
        });

        // Account Recovery routes
        Route::prefix('recovery')->group(function () {
            Route::post('by-code', [App\Http\Controllers\AccountRecoveryController::class, 'recoverByCode']);
            Route::post('question', [App\Http\Controllers\AccountRecoveryController::class, 'getSecurityQuestion']);
            Route::post('verify-answer', [App\Http\Controllers\AccountRecoveryController::class, 'verifySecurityAnswer']);
        });
    });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('security/settings', [App\Http\Controllers\AccountRecoveryController::class, 'updateSecuritySettings']);
});
