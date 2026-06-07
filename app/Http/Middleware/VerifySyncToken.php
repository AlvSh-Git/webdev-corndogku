<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards the Sync API. The peer must present the shared secret as
 * `Authorization: Bearer <SYNC_TOKEN>`. Constant-time compare avoids leaking the
 * token via timing. Must be used over HTTPS (cPanel has TLS).
 */
class VerifySyncToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('sync.peer.token');
        $provided = (string) $request->bearerToken();

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            abort(401, 'Invalid sync token.');
        }

        return $next($request);
    }
}
