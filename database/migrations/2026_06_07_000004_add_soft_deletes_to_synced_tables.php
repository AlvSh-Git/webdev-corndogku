<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Soft deletes for the tables the app actually deletes (users, products,
 * payments). A hard-deleted row just vanishes, which the sync engine cannot
 * tell apart from "not synced yet" — so deletions would fail to propagate or
 * resurrect. `deleted_at` turns a delete into a syncable tombstone.
 *
 * Bonus: soft-deleting a product no longer triggers the order_items
 * cascadeOnDelete, so historical order line items are preserved.
 */
return new class extends Migration
{
    private array $tables = ['users', 'products', 'payments'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
