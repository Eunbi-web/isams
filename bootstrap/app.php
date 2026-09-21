<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL');

// Serverless/read-only filesystems (Vercel) cannot use the repo storage dir
// for compiled views, sessions or logs. Fall back to /tmp whenever the
// Vercel flag is present OR the repo storage dir is not writable.
$repoStorageWritable = is_writable(dirname(__DIR__).'/storage');

if ($isVercel || !$repoStorageWritable) {
    $storage = '/tmp/laravel';

    $directories = [
        $storage,
        $storage.'/framework',
        $storage.'/framework/cache',
        $storage.'/framework/cache/data',
        $storage.'/framework/sessions',
        $storage.'/framework/views',
        $storage.'/logs',
        $storage.'/bootstrap',
        $storage.'/bootstrap/cache',
    ];

    foreach ($directories as $directory) {
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    // Point every framework cache manifest at the writable /tmp copy.
    // bootstrap/cache/*.php must never be committed (dev-only providers leak
    // in otherwise); on a read-only filesystem Laravel rebuilds these at
    // runtime instead of crashing.
    foreach ([
        'APP_SERVICES_CACHE' => $storage.'/bootstrap/cache/services.php',
        'APP_PACKAGES_CACHE' => $storage.'/bootstrap/cache/packages.php',
        'APP_CONFIG_CACHE' => $storage.'/bootstrap/cache/config.php',
        'APP_ROUTES_CACHE' => $storage.'/bootstrap/cache/routes-v7.php',
        'APP_EVENTS_CACHE' => $storage.'/bootstrap/cache/events.php',
    ] as $key => $path) {
        putenv($key.'='.$path);
        $_ENV[$key] = $path;
        $_SERVER[$key] = $path;
    }
}

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
        // Trust the platform proxy (Vercel/Render/Docker) so https is
        // detected from X-Forwarded-Proto and generated URLs stay https.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {})
    ->create();

if ($isVercel || !$repoStorageWritable) {
    $app->useStoragePath('/tmp/laravel');
}

return $app;
