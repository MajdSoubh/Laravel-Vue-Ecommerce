<?php

use Illuminate\Support\Facades\Route;

// Admin
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CategoriesController as AdminCategoryController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ReportController;
// User
use App\Http\Controllers\User\AuthController as UserAuthController;
use App\Http\Controllers\User\CategoryController as UserCategoryController;
use App\Http\Controllers\User\ProductController as UserProductController;
use App\Http\Controllers\User\OrderController as UserOrderController;
use App\Http\Controllers\User\ProfileController as UserProfileController;
use App\Http\Controllers\User\CartController;
use App\Http\Controllers\User\CheckoutController;
use Illuminate\Support\Facades\Artisan;

// Route::get('/user', function (Request $request)
// {
//     return $request->user();
// })->middleware('auth:sanctum');

// Admin Auth Routes
Route::group([
    'prefix' => '/admin',
    'as' => 'admin.',
    'controller' => AdminAuthController::class
], function ()
{
    Route::middleware(['guest:sanctum,admin'])->group(function ()
    {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('register');
    });
    Route::middleware(['auth:sanctum,admin'])->group(function ()
    {
        Route::get('/user', 'getUser')->name('user');
        Route::get('/logout', 'logout')->name('logout');
    });
});

// Admin CRUD
Route::group([
    'middleware' => 'auth:sanctum,admin',
    'prefix' => '/admin',
    'as' => 'admin.',
], function ()
{

    // Category
    Route::get('/category/{category}', [AdminCategoryController::class, 'show']);
    Route::get('/category', [AdminCategoryController::class, 'index']);
    Route::post('/category', [AdminCategoryController::class, 'store']);
    Route::post('/put/category/{category}', [AdminCategoryController::class, 'update']);
    Route::post('/delete/category/{category}', [AdminCategoryController::class, 'destroy']);
    Route::get('categories/tree', [AdminCategoryController::class, 'getAsTree']);

    // Product
    Route::get('/product/{product}', [AdminProductController::class, 'show']);
    Route::get('/product', [AdminProductController::class, 'index']);
    Route::post('/product', [AdminProductController::class, 'store']);
    Route::post('/put/product/{product}', [AdminProductController::class, 'update']);
    Route::post('/delete/product/{product}', [AdminProductController::class, 'destroy']);

    // User
    Route::get('/user/{user}', [UserController::class, 'show']);
    Route::get('/user', [UserController::class, 'index']);
    Route::post('/user', [UserController::class, 'store']);
    Route::post('/put/user/{user}', [UserController::class, 'update']);
    Route::post('/delete/user/{user}', [UserController::class, 'destroy']);

    // Order
    Route::get('/order/{order}', [AdminOrderController::class, 'show']);
    Route::get('/order', [AdminOrderController::class, 'index']);
    Route::post('/order', [AdminOrderController::class, 'store']);
    Route::post('/put/order/{order}', [AdminOrderController::class, 'update']);
    Route::post('/delete/order/{order}', [AdminOrderController::class, 'destroy']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Report
    Route::get('/report', [ReportController::class, 'index']);
});

// User Auth Routes
Route::group([
    'as' => 'user.',
    'controller' => UserAuthController::class
], function ()
{
    Route::middleware('guest:sanctum,client')->group(function ()
    {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('store');
    });
    Route::middleware(['auth:sanctum,client'])->group(function ()
    {
        Route::get('/user', 'getUser');
        Route::get('/logout', 'logout')->name('logout');
    });
});

// User Routes
Route::group([
    'as' => 'user.',
    'middleware' => 'auth:sanctum,client',
], function ()
{
    Route::get('/cart', [CartController::class, 'getCurrentUserCart']);
    Route::post('/cart', [CartController::class, 'setCart']);
    Route::post('/delete/cart/{item}', [CartController::class, 'delete']);
    Route::apiResource('/order', UserOrderController::class);
    Route::post('/checkout/{order}', [CheckoutController::class, 'checkoutOrder']);
    Route::post('/checkout', [CheckoutController::class, 'checkout']);
    Route::post('/checkout/success', [CheckoutController::class, 'success']);

    // User Profile
    Route::post("/password", [UserProfileController::class, 'updatePassword']);
    Route::get("/country", [UserProfileController::class, 'getCountries']);
    Route::post("/customer/details", [UserProfileController::class, 'updateDetails']);
    Route::get("/customer/details", [UserProfileController::class, 'getDetails']);
});
Route::get('/category', [UserCategoryController::class, 'index']);
Route::post('/put/cart', [CartController::class, 'updateCart'])->withoutMiddleware('auth');
Route::get('/product', [UserProductController::class, 'index'])->withoutMiddleware('auth');;
Route::get('/product/{product}', [UserProductController::class, 'show'])->withoutMiddleware('auth');;
Route::post('/payment/success', [CheckoutController::class, 'success']);
Route::get('/seed', function ()
{
    return Artisan::call('db:seed');
});
