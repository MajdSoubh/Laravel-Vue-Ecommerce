<?php

use Illuminate\Support\Facades\Route;

// Admin
use App\Http\Controllers\Admin\AuthController as AdminAuthController;

// User
use App\Http\Controllers\User\AuthController as UserAuthController;



// Admin Auth Routes
Route::group([
    'prefix' => '/admin',
    'as' => 'admin.',
    'controller' => AdminAuthController::class,
    'middleware' => 'throttle:5,1'
], function ()
{
    Route::middleware(['guest:sanctum,admin'])->group(function ()
    {
        Route::post('/login', 'login')->name('login');
        Route::post('/forget-password', 'forgetPassword')->name('password.forget');
        Route::post('/reset-password/{token?}', 'resetPassword')->name('password.reset');
    });
    Route::middleware(['auth:sanctum,admin'])->group(function ()
    {
        Route::get('/user', 'getUser')->name('user');
        Route::get('/logout', 'logout')->name('logout');
    });
});

// User Auth Routes
Route::group([
    'as' => 'user.',  'controller' => UserAuthController::class,
    'middleware' => 'throttle:5,1'
], function ()
{
    Route::middleware('guest:sanctum,client')->group(function ()
    {
        Route::post('/login', 'login')->name('login');
        Route::post('/register', 'register')->name('store');
        Route::post('/forget-password', 'forgetPassword')->name('password.forget');
        Route::post('/reset-password/{token?}', 'resetPassword')->name('password.reset');
    });
    Route::middleware(['auth:sanctum,client'])->group(function ()
    {
        Route::get('/user', 'getUser');
        Route::get('/logout', 'logout')->name('logout');
    });
});
