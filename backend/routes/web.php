<?php

use App\Http\Controllers\User\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

Route::get('/', function ()
{
    return view('welcome');
});
