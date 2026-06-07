<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Sync\SyncApplier;
use App\Sync\SyncReader;
use App\Sync\SyncSchema;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * The passive side of the sync (runs on cPanel). It exposes the peer's changes
 * and accepts batches to apply, using the exact same SyncReader / SyncApplier
 * the active node uses — so both directions stay symmetric. All work happens on
 * this installation's default database connection.
 */
class SyncController extends Controller
{
    /** GET /api/sync/pull?table=orders&since=2026-06-07 10:00:00 */
    public function pull(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table' => ['required', Rule::in(SyncSchema::ordered())],
            'since' => ['nullable', 'date'],
        ]);

        $rows = (new SyncReader($this->connection()))
            ->changes($validated['table'], $validated['since'] ?? null);

        return response()->json([
            'table' => $validated['table'],
            'rows'  => $rows,
        ]);
    }

    /** POST /api/sync/push  { table, rows: [...], dry_run? } */
    public function push(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'table'   => ['required', Rule::in(SyncSchema::ordered())],
            'rows'    => ['present', 'array'],
            'dry_run' => ['sometimes', 'boolean'],
        ]);

        $stats = (new SyncApplier($this->connection(), (bool) ($validated['dry_run'] ?? false)))
            ->apply($validated['table'], $validated['rows']);

        return response()->json([
            'table' => $validated['table'],
            'stats' => $stats,
        ]);
    }

    /** This node treats its own default connection as "local". */
    private function connection(): string
    {
        return config('database.default');
    }
}
