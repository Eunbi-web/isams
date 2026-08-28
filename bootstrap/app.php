<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {})
    ->create();

/*
|--------------------------------------------------------------------------
| Vercel writable storage
|--------------------------------------------------------------------------
*/

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $tmpStorage = '/tmp/laravel';

    if (!is_dir($tmpStorage)) {
        mkdir($tmpStorage, 0755, true);
    }

    $app->useStoragePath($tmpStorage);
}

return $app;