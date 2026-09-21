<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

$isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL');

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Temporary production diagnostic (no session/auth middleware).
        // Reports environment writability + DB reachability, never secrets.
        // TODO: remove once the Vercel 500 is resolved.
        then: function () {
            Route::get('/diag-runtime', function () {
                $result = ['ok' => true];
                try {
                    $result['php'] = PHP_VERSION;
                    $result['vercel_getenv'] = getenv('VERCEL') ?: null;
                    $result['vercel_ENV'] = $_ENV['VERCEL'] ?? null;
                    $result['vercel_SERVER'] = $_SERVER['VERCEL'] ?? null;
                    $result['storage_path'] = storage_path();
                    $result['storage_writable'] = is_writable(storage_path());
                    $viewsDir = storage_path('framework/views');
                    $result['views_dir'] = $viewsDir;
                    $result['views_dir_exists'] = is_dir($viewsDir);
                    $result['views_writable'] = is_writable($viewsDir);
                    $probe = $viewsDir.'/.write-test';
                    $result['views_write_probe'] = @file_put_contents($probe, 'x') !== false
                        ? (@unlink($probe) ? 'OK' : 'OK (cleanup failed)')
                        : 'FAIL';
                    $result['app_key_present'] = (bool) config('app.key');
                    $result['app_debug'] = config('app.debug');
                    $result['session_driver'] = config('session.driver');
                    $result['cache_default'] = config('cache.default');
                    $result['queue_default'] = config('queue.default');
                    $result['db_connection'] = config('database.default');
                    $result['db_host'] = config('database.connections.pgsql.host');
                    $result['ext_pdo_pgsql'] = extension_loaded('pdo_pgsql');
                    try {
                        DB::connection()->getPdo();
                        $result['db'] = 'OK';
                    } catch (Throwable $e) {
                        $result['db'] = get_class($e).': '.substr($e->getMessage(), 0, 300);
                    }
                    try {
                        DB::table('sessions')->limit(1)->count();
                        $result['sessions_table'] = 'OK';
                    } catch (Throwable $e) {
                        $result['sessions_table'] = get_class($e).': '.substr($e->getMessage(), 0, 300);
                    }
                    try {
                        $result['login_view_exists'] = \Illuminate\Support\Facades\View::exists('auth.login');
                    } catch (Throwable $e) {
                        $result['login_view_exists'] = get_class($e).': '.substr($e->getMessage(), 0, 300);
                    }
                } catch (Throwable $e) {
                    $result = ['ok' => false, 'error' => get_class($e).': '.substr($e->getMessage(), 0, 500)];
                }
                return response()->json($result);
            });
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class
        ]);
        // Trust the platform proxy (Vercel/Render/Docker) so https is
        // detected from X-Forwarded-Proto and generated URLs stay https.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // TEMPORARY (Vercel 500 diagnosis): surface server-error details as
        // JSON instead of the generic page. REMOVE once resolved.
        $exceptions->render(function (Throwable $e, $request) {
            $status = $e instanceof Symfony\Component\HttpKernel\Exception\HttpException
                ? $e->getStatusCode()
                : 500;
            if ($status >= 500) {
                return response()->json([
                    'vercel_500_diag' => true,
                    'error' => get_class($e),
                    'message' => substr($e->getMessage(), 0, 1000),
                    'file' => $e->getFile().':'.$e->getLine(),
                ], 500);
            }
        });
    })
    ->create();

$isVercel = isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']) || getenv('VERCEL');

// Serverless/read-only filesystems (Vercel) cannot use the repo storage dir
// for compiled views, sessions or logs. Fall back to /tmp whenever the
// Vercel flag is present OR the repo storage dir is not writable.
$repoStorageWritable = is_writable(dirname(__DIR__).'/storage');

if ($isVercel || !$repoStorageWritable) {
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