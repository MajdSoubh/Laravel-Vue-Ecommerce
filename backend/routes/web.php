<?php

use App\Http\Controllers\User\CheckoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;

Route::get('/', function ()
{
    return view('welcome');
});
Route::get('/s', function ()
{
    return shell_exec('chown -R www-data:www-data /var/www/html \
    && chmod -R 777 /var/www/html \
    && chmod -R 777 /var/www/html/public/  \
    && chmod -R 777 /var/www/html/storage/;');
});
