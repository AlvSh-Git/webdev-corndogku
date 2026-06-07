<?php

namespace App\Sync\Peers;

use App\Sync\Contracts\SyncPeer;
use Illuminate\Support\Facades\Http;

/**
 * Production peer: talks to the cPanel Sync API over HTTPS with a bearer token.
 * The endpoints it calls (GET /api/sync/pull, POST /api/sync/push) run the same
 * SyncSerializer / SyncApplier on the cPanel side — see Step 5.
 */
class HttpPeer implements SyncPeer
{
    public function __construct(
        private string $baseUrl,
        private string $token,
        private string $name = 'cpanel',
        private int $timeout = 20,
        private bool $dryRun = false,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    private function client()
    {
        return Http::baseUrl(rtrim($this->baseUrl, '/'))
            ->withToken($this->token)
            ->acceptJson()
            ->timeout($this->timeout);
    }

    public function pullChanges(string $table, ?string $since): array
    {
        $response = $this->client()->get('/api/sync/pull', [
            'table' => $table,
            'since' => $since,
        ])->throw();

        return $response->json('rows', []);
    }

    public function pushChanges(string $table, array $payloads): array
    {
        $response = $this->client()->post('/api/sync/push', [
            'table'   => $table,
            'rows'    => $payloads,
            'dry_run' => $this->dryRun,
        ])->throw();

        return $response->json('stats', []);
    }
}
