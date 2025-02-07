<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\VerificationController;

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
    });
    Route::middleware(['auth:sanctum,client'])->group(function ()
    {
        Route::get('/user', 'getUser');
        Route::get('/logout', 'logout')->name('logout');
    });
});

// Password Reset Routes
Route::group([
    'controller' => PasswordResetController::class,
    'middleware' => 'throttle:10,1'
], function ()
{
    Route::middleware('guest:sanctum,client')->group(function ()
    {
        Route::post('/forget-password', 'forgetPassword')->name('password.forget');
        Route::post('/reset-password/{token?}', 'resetPassword')->name('password.reset');
    });
});

// Email Verification Routes
Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verifyEmail'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/resend', [VerificationController::class, 'resendVerificationEmail'])
    ->middleware(['auth:sanctum,client,admin', 'throttle:6,1'])
    ->name('verification.resend');
