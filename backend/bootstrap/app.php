<?php

use App\Http\Middleware\SetLocale;
use App\Http\Middleware\TrackUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        channels: __DIR__ . '/../routes/channels.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware)
    {
        $middleware->alias([
            'guest' => App\Http\Middleware\Guest::class,
            'auth' => App\Http\Middleware\Auth::class
        ]);

        $middleware->append([SetLocale::class, TrackUser::class]);
    })
    ->withExceptions(function (Exceptions $exceptions)
    {
        //
    })->create();
