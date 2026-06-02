<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Midtrans posts server-to-server (no session/cookie) → exempt from CSRF.
        // The endpoint authenticates itself via the SHA-512 signature_key instead.
        $middleware->validateCsrfTokens(except: [
            'midtrans/notification',
        ]);

        // RBAC alias — used as role:owner / role:cashier,owner on route groups.
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // Unauthenticated users hitting a protected route are sent to login with
        // a flash message. (AJAX/JSON requests still receive a 401 instead.)
        $middleware->redirectGuestsTo(function (\Illuminate\Http\Request $request) {
            session()->flash('error', 'Silakan login terlebih dahulu.');
            return route('login');
        });
        $middleware->redirectUsersTo(function (\Illuminate\Http\Request $request) {
            $user = $request->user();
            if (! $user) {
                return '/';
            }
            return match ($user->role) {
                'owner'   => route('owner.dashboard'),
                'cashier' => route('cashier.dashboard'),
                default   => '/',
            };
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
