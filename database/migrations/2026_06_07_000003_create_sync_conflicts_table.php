<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Audit trail for the sync. Every last-write-wins decision and every failure is
 * recorded here so nothing is ever silently overwritten without a trace, and so
 * we can review conflicts during the first days of operation.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_conflicts', function (Blueprint $table) {
            $table->id();
            $table->string('table_name');
            $table->ulid('row_ulid')->nullable();
            $table->enum('resolution', ['local_won', 'remote_won', 'error', 'deferred']);
            $table->timestamp('local_updated_at')->nullable();
            $table->timestamp('remote_updated_at')->nullable();
            $table->text('detail')->nullable();           // error message / note
            $table->json('payload')->nullable();          // the incoming row, for inspection
            $table->timestamps();

            $table->index(['table_name', 'row_ulid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_conflicts');
    }
};
