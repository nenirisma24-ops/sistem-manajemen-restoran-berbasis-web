<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleBasedAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = Auth::user();

        if (! $user) {
            abort(403);
        }

        $userRole = strtolower((string) ($user->role ?? 'customer'));

        if ($roles === []) {
            return $next($request);
        }

        if (! in_array($userRole, array_map('strtolower', $roles), true)) {
            abort(403);
        }

        return $next($request);
    }
}
