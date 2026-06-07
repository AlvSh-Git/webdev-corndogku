<?php

namespace App\Sync;

use Illuminate\Support\Facades\DB;

/**
 * Turns a database row into a portable, cross-database payload: every foreign
 * key is replaced by the parent row's ULID so it means the same thing on the
 * other database (where integer ids differ). The local `id` is dropped entirely.
 */
class SyncSerializer
{
    public function __construct(private string $connection) {}

    public function payload(string $table, object $row): array
    {
        $out = [];
        foreach (SyncSchema::dataColumns($this->connection, $table) as $col) {
            $out[$col] = $row->{$col};
        }

        foreach (SyncSchema::fks($table) as $col => $parentTable) {
            $out[$col . '_ulid'] = $this->parentUlid($parentTable, $row->{$col} ?? null);
        }

        return $out;
    }

    private function parentUlid(string $parentTable, $parentId): ?string
    {
        if ($parentId === null) {
            return null;
        }

        return DB::connection($this->connection)
            ->table($parentTable)
            ->where('id', $parentId)
            ->value('ulid');
    }
}
