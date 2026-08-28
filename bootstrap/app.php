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

if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
    $storage = '/tmp/laravel';

    $directories = [
        $storage,
        $storage . '/framework',
        $storage . '/framework/cache',
        $storage . '/framework/cache/data',
        $storage . '/framework/sessions',
        $storage . '/framework/views',
        $storage . '/logs',
    ];

    foreach ($directories as $directory) {
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    $app->useStoragePath($storage);
}

return $app;