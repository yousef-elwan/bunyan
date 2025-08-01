<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: [
            __DIR__ . '/../routes/web.php',
            __DIR__ . '/../routes/dashboard.php',
            __DIR__ . '/../routes/global.php'
        ],
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        channels: __DIR__ . '/../routes/channels.php',
    )
    ->withBroadcasting(
        channels: __DIR__ . '/../routes/channels.php',
    )->withMiddleware(function (Middleware $middleware) {
        $middleware->web([
            \App\Http\Middleware\DetectRequestType::class,
        ]);
        $middleware->api([
            \App\Http\Middleware\DetectRequestType::class,
            \App\Http\Middleware\ConvertEmptyStringsAndNullStringsToNull::class,
        ]);
        $middleware->alias([
            'lang' => App\Http\Middleware\SetLanguageMiddleware::class,
            'localeFromUrl' => App\Http\Middleware\SetLocaleFromUrl::class,
            'setWebConfig' => App\Http\Middleware\SetWebConfigMiddleware::class,
            'role' => \App\Http\Middleware\CheckUserRole::class,
            'optional.sanctum' => \App\Http\Middleware\OptionalSanctumAuth::class,
        ]);
        $middleware->append([
            Illuminate\Http\Middleware\HandleCors::class
        ]);
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();
