<?php

namespace App\Console\Commands;

use App\Sync\Peers\DbPeer;
use App\Sync\Peers\HttpPeer;
use App\Sync\SyncEngine;
use Illuminate\Console\Command;

/**
 * Runs one sync cycle. On the local POS this is scheduled every minute; it can
 * also be run by hand for a dry-run or one-off.
 *
 *   php artisan sync:run                      # production: pull+push over HTTPS
 *   php artisan sync:run --dry-run            # show what would change, write nothing
 *   php artisan sync:run --driver=db          # rehearsal against the cpanel_copy clone
 *   php artisan sync:run --direction=pull     # one direction only
 */
class SyncRun extends Command
{
    protected $signature = 'sync:run
        {--driver= : http (default, real peer) or db (rehearsal against cpanel_copy)}
        {--direction=both : both|pull|push}
        {--local-connection= : connection to treat as "this" node (defaults to app default)}
        {--dry-run : compute and report changes without writing}';

    protected $description = 'Run one database sync cycle with the peer';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $driver = $this->option('driver') ?: 'http';
        $direction = $this->option('direction');
        $local = $this->option('local-connection') ?: config('database.default');

        $peer = match ($driver) {
            'db'   => new DbPeer(config('sync.rehearsal_connection', 'cpanel_copy'), $dryRun),
            'http' => $this->httpPeer($dryRun),
            default => null,
        };

        if (! $peer) {
            $this->error("Unknown --driver [{$driver}]. Use 'http' or 'db'.");
            return self::FAILURE;
        }

        $this->line(sprintf(
            '<info>sync:run</info> driver=%s direction=%s local=%s%s',
            $driver, $direction, $local, $dryRun ? ' <comment>[dry-run]</comment>' : ''
        ));

        $engine = new SyncEngine($peer, $local, $dryRun);

        $report = match ($direction) {
            'pull' => ['pull' => $engine->pull()],
            'push' => ['push' => $engine->push()],
            default => $engine->run(),
        };

        foreach ($report as $dir => $tables) {
            $this->renderDirection($dir, $tables);
        }

        return self::SUCCESS;
    }

    private function httpPeer(bool $dryRun): ?HttpPeer
    {
        $peer = config('sync.peer');
        if (empty($peer['base_url']) || empty($peer['token'])) {
            $this->error('SYNC_PEER_URL and SYNC_TOKEN must be set for the http driver.');
            return null;
        }

        return new HttpPeer($peer['base_url'], $peer['token'], $peer['name'] ?? 'cpanel', $peer['timeout'] ?? 20, $dryRun);
    }

    private function renderDirection(string $dir, array $tables): void
    {
        $this->newLine();
        $this->line("<comment>".strtoupper($dir)."</comment>");

        $rows = [];
        $totals = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'deferred' => 0, 'conflicts' => 0];
        foreach ($tables as $table => $s) {
            $rows[] = [
                $table,
                $s['fetched'] ?? $s['sent'] ?? 0,
                $s['inserted'] ?? 0,
                $s['updated'] ?? 0,
                $s['skipped'] ?? 0,
                $s['deferred'] ?? 0,
                $s['conflicts'] ?? 0,
            ];
            foreach ($totals as $k => $_) {
                $totals[$k] += $s[$k] ?? 0;
            }
        }
        $rows[] = ['<info>TOTAL</info>', '', $totals['inserted'], $totals['updated'], $totals['skipped'], $totals['deferred'], $totals['conflicts']];

        $this->table(['table', 'rows', 'ins', 'upd', 'skip', 'defer', 'confl'], $rows);
    }
}
