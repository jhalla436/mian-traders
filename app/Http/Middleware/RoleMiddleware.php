<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'No permission.');
        }

        // If user is disabled
        if (property_exists($user, 'is_active') && (int)$user->is_active !== 1) {
            abort(403, 'Account disabled.');
        }

        $role = (string)($user->role ?? '');

        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array($role, $roles, true)) {
            abort(403, 'No permission. admin only');
        }

        return $next($request);
    }
}
