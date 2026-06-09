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
}
