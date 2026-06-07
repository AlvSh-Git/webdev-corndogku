<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * One-time backfill of `ulid` for rows that existed before the column was added.
 * New rows get their ulid automatically via the HasUlid trait; this fills the
 * gap for pre-existing data. Safe to re-run — it only touches rows where ulid
 * is still NULL. Run on each database (local, and later cPanel) after migrating.
 *
 *   php artisan sync:backfill-ulids
 *   php artisan sync:backfill-ulids --connection=cpanel_copy   (staging rehearsal)
 */
class SyncBackfillUlids extends Command
{
    protected $signature = 'sync:backfill-ulids {--connection= : DB connection to use (defaults to app default)}';

    protected $description = 'Assign a ULID to any synced-table rows that are missing one';

    private array $tables = [
        'users',
        'categories',
        'products',
        'components',
        'orders',
        'order_items',
        'payments',
        'chatbot_logs',
    ];

    public function handle(): int
    {
        $connection = $this->option('connection') ?: config('database.default');
        $db = DB::connection($connection);
        $this->info("Backfilling ULIDs on connection [{$connection}]");

        $grandTotal = 0;

        foreach ($this->tables as $table) {
            $filled = 0;

            $db->table($table)
                ->whereNull('ulid')
                ->orderBy('id')
                ->chunkById(500, function ($rows) use ($db, $table, &$filled) {
                    foreach ($rows as $row) {
                        $db->table($table)
                            ->where('id', $row->id)
                            ->update(['ulid' => (string) Str::ulid()]);
                        $filled++;
                    }
                });

            $this->line(sprintf('  %-14s %d row(s)', $table, $filled));
            $grandTotal += $filled;
        }

        $this->info("Done. {$grandTotal} row(s) backfilled.");

        return self::SUCCESS;
    }
}
