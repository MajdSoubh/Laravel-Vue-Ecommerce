<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Admin Auth Routes
Route::group([
    'prefix' => '/admin',
    'as' => 'admin.',
    'controller' => AuthController::class,
    'middleware' => 'throttle:10,1'
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
    'as' => 'user.',
    'controller' => AuthController::class,
    'middleware' => 'throttle:10,1'
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
