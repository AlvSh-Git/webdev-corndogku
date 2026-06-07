<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cross-database row identity for sync.
 *
 * Local and cPanel auto-increment their integer `id` columns independently, so
 * `id = 5` is a different row on each side. `ulid` is a stable identity that
 * means the same row on both databases; the sync engine matches rows and
 * translates foreign keys through it. Nullable + unique here so it can be added
 * to tables that already contain rows; `sync:backfill-ulids` then populates them.
 */
return new class extends Migration
{
    private array $tables = [
        'users',
        'categories',
        'products',
        'components',
        'orders',
        'order_items',
        'payments',
        'chatbot_logs',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->ulid('ulid')->nullable()->unique()->after('id');
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropUnique(['ulid']);
                $table->dropColumn('ulid');
            });
        }
    }
};
