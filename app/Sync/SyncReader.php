<?php

namespace App\Sync;

use Illuminate\Support\Facades\DB;

/**
 * Reads a table's changes on a connection and serializes them to portable
 * payloads. Shared by DbPeer (rehearsal), the engine's push, and the cPanel
 * Sync API's pull endpoint, so every path produces identical payloads.
 */
class SyncReader
{
    public function __construct(private string $connection) {}

    public function changes(string $table, ?string $since): array
    {
        $query = DB::connection($this->connection)->table($table);
        if ($since !== null) {
            $query->where('updated_at', '>=', $since);
        }
        $rows = $query->orderBy('updated_at')->orderBy('ulid')->get();

        $serializer = new SyncSerializer($this->connection);

        return $rows->map(fn ($row) => $serializer->payload($table, $row))->all();
    }
}
