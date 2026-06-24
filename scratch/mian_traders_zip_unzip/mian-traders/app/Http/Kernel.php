<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

/**
 * NOTE:
 * Laravel 11/12 no longer relies on App\Http\Kernel for middleware registration.
 * Middleware aliases/groups are configured in bootstrap/app.php.
 *
 * This file is kept only for compatibility with older tooling / packages that
 * might still try to resolve App\Http\Kernel.
 */
class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        //
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            //
        ],
        'api' => [
            //
        ],
    ];

    /**
     * The application's route middleware aliases.
     *
     * @var array<string, class-string|string>
     */
    protected $middlewareAliases = [
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ];
}
