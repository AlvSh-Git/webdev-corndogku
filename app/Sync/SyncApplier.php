<?php

namespace App\Sync;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Applies a batch of incoming payloads to a target connection.
 *
 * Per row: resolve FK ULIDs to local parent ids (defer if a parent isn't here
 * yet), then insert if the ULID is new, or last-write-wins update by updated_at
 * if it already exists. Soft deletes ride along as the `deleted_at` column, so a
 * tombstone propagates like any other change. Every overwrite and every deferral
 * is recorded in `sync_conflicts` for audit.
 */
class SyncApplier
{
    public function __construct(
        private string $connection,
        private bool $dryRun = false,
    ) {}

    public function apply(string $table, array $payloads): array
    {
        $stats = ['inserted' => 0, 'updated' => 0, 'skipped' => 0, 'deferred' => 0, 'conflicts' => 0];
        $conn = DB::connection($this->connection);
        $fks = SyncSchema::fks($table);

        foreach ($payloads as $payload) {
            $payload = (array) $payload;
            $ulid = $payload['ulid'] ?? null;
            if ($ulid === null) {
                continue; // a row with no identity cannot be synced
            }

            // Build the attribute set: data columns minus the *_ulid FK markers.
            $attrs = [];
            foreach ($payload as $key => $value) {
                if (! str_ends_with($key, '_ulid') || $key === 'ulid') {
                    $attrs[$key] = $value;
                }
            }

            // Resolve foreign keys; defer the row if a parent isn't present yet.
            $deferred = false;
            foreach ($fks as $col => $parentTable) {
                $parentUlid = $payload[$col . '_ulid'] ?? null;
                if ($parentUlid === null) {
                    $attrs[$col] = null;
                    continue;
                }
                $parentId = $conn->table($parentTable)->where('ulid', $parentUlid)->value('id');
                if ($parentId === null) {
                    $deferred = true;
                    break;
                }
                $attrs[$col] = $parentId;
            }

            if ($deferred) {
                $stats['deferred']++;
                $this->logConflict($table, $ulid, 'deferred', null, $payload['updated_at'] ?? null, $payload);
                continue;
            }

            $existing = $conn->table($table)->where('ulid', $ulid)->first();

            if (! $existing) {
                if (! $this->dryRun) {
                    $conn->table($table)->insert($attrs);
                }
                $stats['inserted']++;
                continue;
            }

            $incoming = $payload['updated_at'] ?? null;
            $local = $existing->updated_at ?? null;

            if ($this->isNewer($incoming, $local)) {
                if (! $this->dryRun) {
                    unset($attrs['ulid']); // never rewrite identity
                    $conn->table($table)->where('ulid', $ulid)->update($attrs);
                    $this->logConflict($table, $ulid, 'remote_won', $local, $incoming, $payload);
                }
                $stats['updated']++;
                $stats['conflicts']++;
            } else {
                $stats['skipped']++;
            }
        }

        return $stats;
    }

    /** Strictly-newer comparison; a null/absent local timestamp counts as oldest. */
    private function isNewer(?string $incoming, ?string $local): bool
    {
        if ($incoming === null) {
            return false;
        }
        if ($local === null) {
            return true;
        }

        return Carbon::parse($incoming)->gt(Carbon::parse($local));
    }

    private function logConflict(string $table, ?string $ulid, string $resolution, ?string $localAt, ?string $remoteAt, array $payload): void
    {
        if ($this->dryRun) {
            return;
        }

        try {
            DB::connection($this->connection)->table('sync_conflicts')->insert([
                'table_name'        => $table,
                'row_ulid'          => $ulid,
                'resolution'        => $resolution,
                'local_updated_at'  => $localAt,
                'remote_updated_at' => $remoteAt,
                'detail'            => null,
                'payload'           => json_encode($payload),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let audit logging break a sync run.
        }
    }
}
