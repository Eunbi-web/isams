<?php
use Illuminate\Support\Str;
return [
    'driver'          => env('SESSION_DRIVER', 'file'),
    'lifetime'        => env('SESSION_LIFETIME', 120),
    'expire_on_close' => env('SESSION_EXPIRE_ON_CLOSE', false),

    /*
    |--------------------------------------------------------------------------
    | Encrypt Session
    |--------------------------------------------------------------------------
    |
    | This option allows you to easily specify that all of your session data
    | should be encrypted. When enabled, Laravel will automatically encrypt
    | the session data before storing it. It is highly recommended to
    | enable this in production if you store sensitive information.
    |
    */
    'encrypt'         => env('SESSION_ENCRYPT', false),
    'files'           => storage_path('framework/sessions'),
    'connection'      => env('SESSION_CONNECTION'),
    'table'           => env('SESSION_TABLE', 'sessions'),
    'store'           => env('SESSION_STORE'),
    'lottery'         => [2, 100],
    'cookie'          => env('SESSION_COOKIE', Str::slug(env('APP_NAME', 'laravel'), '_').'_session'),
    'path'            => env('SESSION_PATH', '/'),
    'domain'          => env('SESSION_DOMAIN'),

    /*
    |--------------------------------------------------------------------------
    | Secure Session Cookie
    |--------------------------------------------------------------------------
    |
    | When this option is set to true, the session cookie will only be sent
    | back to the server if the browser has a HTTPS connection. This will
    | prevent the cookie from being sent to you if it can not be done
    | securely. It is highly recommended to set this to true for
    | production environments.
    |
    */
    'secure'          => env('SESSION_SECURE_COOKIE'),
    'http_only'       => env('SESSION_HTTP_ONLY', true),
    'same_site'       => env('SESSION_SAME_SITE', 'lax'),
    'partitioned'     => env('SESSION_PARTITIONED_COOKIE', false),
];
