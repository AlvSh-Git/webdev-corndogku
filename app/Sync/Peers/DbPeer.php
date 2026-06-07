<?php

namespace App\Sync\Peers;

use App\Sync\Contracts\SyncPeer;
use App\Sync\SyncApplier;
use App\Sync\SyncReader;

/**
 * Rehearsal peer: the "other database" is just another local connection
 * (the cpanel_copy clone). Lets us exercise the full engine offline with no
 * HTTP involved. Production uses HttpPeer instead, with identical semantics.
 */
class DbPeer implements SyncPeer
{
    public function __construct(
        private string $connection = 'cpanel_copy',
        private bool $dryRun = false,
    ) {}

    public function name(): string
    {
        return $this->connection;
    }

    public function pullChanges(string $table, ?string $since): array
    {
        return (new SyncReader($this->connection))->changes($table, $since);
    }

    public function pushChanges(string $table, array $payloads): array
    {
        return (new SyncApplier($this->connection, $this->dryRun))->apply($table, $payloads);
    }
}
