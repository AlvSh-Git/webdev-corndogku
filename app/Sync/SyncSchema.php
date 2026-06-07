<?php

namespace App\Sync;

use Illuminate\Support\Facades\Schema;

/**
 * Single source of truth for what gets synced and how rows relate.
 *
 * For each table we record its foreign keys (local column => parent table) so
 * the serializer can emit FKs as the parent's ULID and the applier can resolve
 * them back to a local id. Order matters: parents are listed before children so
 * a batch inserts in an order that satisfies foreign keys.
 */
class SyncSchema
{
    public static function tables(): array
    {
        return [
            'users'        => ['fks' => [],                                        'softDelete' => true],
            'categories'   => ['fks' => [],                                        'softDelete' => false],
            'products'     => ['fks' => ['category_id' => 'categories'],           'softDelete' => true],
            'components'   => ['fks' => [],                                        'softDelete' => false],
            'orders'       => ['fks' => ['user_id' => 'users', 'cashier_id' => 'users'], 'softDelete' => false],
            'order_items'  => ['fks' => ['order_id' => 'orders', 'product_id' => 'products'], 'softDelete' => false],
            'payments'     => ['fks' => ['order_id' => 'orders'],                  'softDelete' => true],
            'chatbot_logs' => ['fks' => ['user_id' => 'users'],                    'softDelete' => false],
        ];
    }

    /** Table names, parents before children. */
    public static function ordered(): array
    {
        return array_keys(self::tables());
    }

    public static function fks(string $table): array
    {
        return self::tables()[$table]['fks'] ?? [];
    }

    /** Data columns to carry in a payload: every column except the local `id`
     *  and the raw FK columns (which travel as `<col>_ulid` instead). */
    public static function dataColumns(string $connection, string $table): array
    {
        $fks = array_keys(self::fks($table));
        $cols = Schema::connection($connection)->getColumnListing($table);

        return array_values(array_filter(
            $cols,
            fn ($c) => $c !== 'id' && ! in_array($c, $fks, true)
        ));
    }
}
