<?php

use App\Http\Controllers\Api\SyncController;
use Illuminate\Support\Facades\Route;

/*
| Sync API — the passive side of the database sync (served by cPanel). The
| active node (local POS) calls these over HTTPS with a bearer token. See
| App\Sync and config/sync.php.
*/
Route::middleware('sync.token')->prefix('sync')->group(function () {
    Route::get('pull', [SyncController::class, 'pull']);
    Route::post('push', [SyncController::class, 'push']);
});
