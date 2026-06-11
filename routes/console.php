<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Database sync: the local POS is the active agent and runs one cycle every
// minute. withoutOverlapping() prevents a slow run from stacking on the next.
// Only the 'local' role drives sync; cPanel just answers the API.
if (config('sync.role') === 'local') {
    Schedule::command('sync:run')
        ->everyMinute()
        ->withoutOverlapping()
        ->appendOutputTo(storage_path('logs/sync.log'));

    // Safety net for unpaid online orders Midtrans never sent an expire
    // notification for (abandoned at the "select payment" step, before any QR
    // charge) — the webhook handles the charged-then-expired case in real time
    // (~15 min). These never-charged orders get a full 24h grace before the sweep
    // cancels them and frees their reserved stock. Runs on the authoritative
    // 'local' node so it doesn't double-sweep the synced cPanel database.
    Schedule::command('orders:cancel-expired --minutes=1440')
        ->everyFiveMinutes()
        ->withoutOverlapping();
}
