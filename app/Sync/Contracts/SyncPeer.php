<?php

namespace App\Sync\Contracts;

/**
 * The other side of a sync. The engine only ever talks to this interface, so
 * the same engine logic runs whether the peer is reached directly over a
 * database connection (rehearsal) or over HTTPS (production).
 */
interface SyncPeer
{
    /** Serialized payloads for rows in $table changed at/after $since (null = all). */
    public function pullChanges(string $table, ?string $since): array;

    /** Apply a batch of payloads on the peer; returns the peer's apply stats. */
    public function pushChanges(string $table, array $payloads): array;

    /** Human-readable name, used as the key in sync_states. */
    public function name(): string;
}
