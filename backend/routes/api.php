<?php

use Illuminate\Support\Facades\Route;

// Admin
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoriesController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;

// User
use App\Http\Controllers\User\CategoryController as UserCategoryController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use App\Http\Controllers\User\StripeWebhookController;
use Illuminate\Support\Facades\Broadcast;

// Include Auth Routes
require "auth.php";

// Publish Broadcasting Routes
Broadcast::routes(['middleware' => ['auth:sanctum,client,admin']]);

// Admin CRUD Routes
Route::group([
    'prefix' => '/admin',
    'as' => 'admin.',
    'middleware' => ['auth:sanctum,admin', 'throttle:80,1'],
], function ()
{
    // Category
    Route::get('categories/tree', [AdminCategoryController::class, 'getAsTree']);
    Route::apiResource('/categories', AdminCategoryController::class);

    // Product
    Route::apiResource('/products', AdminProductController::class);

    // User
    Route::apiResource('/users', UserController::class);

    // Order
    Route::apiResource('/orders', AdminOrderController::class)->only('show', 'index');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Report
    Route::get('/reports', [ReportController::class, 'index']);
});

// User CRUD Routes
Route::group(['as' => 'user.', 'middleware' => 'throttle:80,1'], function ()
{

    // Authenticated Routes
    Route::group([
        'middleware' => 'auth:sanctum,client',
    ], function ()
    {
        Route::get('/cart', [CartController::class, 'getCurrentUserCart']);
        Route::post('/cart', [CartController::class, 'setCart']);
        Route::delete('/cart/{item}', [CartController::class, 'delete']);
        Route::apiResource('/orders', UserOrderController::class)->only('index', 'show');
        Route::post('/checkout/orders/{order}', [CheckoutController::class, 'checkoutOrder']);
        Route::post('/checkout', [CheckoutController::class, 'checkout']);
        Route::post('/checkout/success', [CheckoutController::class, 'success']);

        // Profile
        Route::post("/password", [UserProfileController::class, 'updatePassword']);
        Route::get("/countries", [UserProfileController::class, 'getCountries']);
        Route::post("/customer/details", [UserProfileController::class, 'updateDetails']);
        Route::get("/customer/details", [UserProfileController::class, 'getDetails']);
    });

    // Public Routes
    Route::get('/categories', [UserCategoryController::class, 'index']);
    Route::put('/cart', [CartController::class, 'updateCart']);
    Route::get('/products', [UserProductController::class, 'index']);
    Route::get('/products/{product}', [UserProductController::class, 'show']);
    Route::post('/payments/success', [CheckoutController::class, 'success']);
    Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
});
