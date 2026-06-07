<?php

namespace App\Sync;

use App\Sync\Contracts\SyncPeer;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Orchestrates one sync cycle between this node and a peer.
 *
 * pull(): fetch the peer's changes since our last watermark and apply locally.
 * push(): send our local changes since our last watermark to the peer.
 *
 * Watermarks live in `sync_states` on the local connection, one per
 * (peer, table, direction). Change detection uses `updated_at >= watermark`
 * (MySQL TIMESTAMP has 1-second resolution, so the boundary second is
 * re-scanned each run); re-applying an unchanged row is a harmless no-op
 * because the applier is last-write-wins and idempotent.
 */
class SyncEngine
{
    private SyncReader $reader;

    public function __construct(
        private SyncPeer $peer,
        private string $localConnection,
        private bool $dryRun = false,
    ) {
        $this->reader = new SyncReader($localConnection);
    }

    public function run(): array
    {
        return [
            'pull' => $this->pull(),
            'push' => $this->push(),
        ];
    }

    public function pull(): array
    {
        $report = [];
        $applier = new SyncApplier($this->localConnection, $this->dryRun);

        foreach (SyncSchema::ordered() as $table) {
            $since = $this->watermark('pull', $table);
            $payloads = $this->peer->pullChanges($table, $since);

            $stats = $applier->apply($table, $payloads);
            $this->advance('pull', $table, $this->maxUpdatedAt($payloads), $since);

            $report[$table] = $stats + ['fetched' => count($payloads)];
        }

        return $report;
    }

    public function push(): array
    {
        $report = [];

        foreach (SyncSchema::ordered() as $table) {
            $since = $this->watermark('push', $table);
            $payloads = $this->reader->changes($table, $since);

            $stats = $this->peer->pushChanges($table, $payloads);
            $this->advance('push', $table, $this->maxUpdatedAt($payloads), $since);

            $report[$table] = $stats + ['sent' => count($payloads)];
        }

        return $report;
    }

    private function maxUpdatedAt(array $payloads): ?string
    {
        $max = null;
        foreach ($payloads as $payload) {
            $value = ((array) $payload)['updated_at'] ?? null;
            if ($value !== null && ($max === null || Carbon::parse($value)->gt(Carbon::parse($max)))) {
                $max = $value;
            }
        }

        return $max;
    }

    private function watermark(string $direction, string $table): ?string
    {
        return DB::connection($this->localConnection)
            ->table('sync_states')
            ->where('peer', $this->peer->name())
            ->where('table_name', $table)
            ->where('direction', $direction)
            ->value('last_synced_at');
    }

    private function advance(string $direction, string $table, ?string $batchMax, ?string $previous): void
    {
        if ($this->dryRun || $batchMax === null) {
            return;
        }
        // Never move the watermark backwards.
        if ($previous !== null && Carbon::parse($batchMax)->lt(Carbon::parse($previous))) {
            $batchMax = $previous;
        }

        DB::connection($this->localConnection)->table('sync_states')->updateOrInsert(
            ['peer' => $this->peer->name(), 'table_name' => $table, 'direction' => $direction],
            ['last_synced_at' => $batchMax, 'updated_at' => now(), 'created_at' => now()],
        );
    }
}
