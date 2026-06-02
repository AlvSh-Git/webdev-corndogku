<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Role-based access control.
 *
 * Usage in routes: ->middleware('role:owner')  or  ->middleware('role:cashier,owner')
 *
 * This middleware assumes 'auth' has already run, so an authenticated user is
 * guaranteed. It only checks that the user's role is one of the allowed roles;
 * otherwise it aborts with 403 (Forbidden) rather than redirecting, since the
 * user IS logged in — they simply lack permission.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
