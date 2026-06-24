<?php

// Safety check: If we are running unit tests, ensure the configuration cache is cleared
// so tests don't run on the active cached local MySQL configuration and wipe data.
if (defined('PHPUNIT_COMPOSER_INSTALL') || (isset($_SERVER['argv']) && (str_contains(implode(' ', $_SERVER['argv']), 'phpunit') || str_contains(implode(' ', $_SERVER['argv']), 'artisan test')))) {
    $configCachePath = __DIR__ . '/cache/config.php';
    if (file_exists($configCachePath)) {
        @unlink($configCachePath);
    }
}

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ✅ IMPORTANT: Register route middleware aliases here (Laravel 11/12)
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'activeShop' => \App\Http\Middleware\EnsureActiveShop::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
