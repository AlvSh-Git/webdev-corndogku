<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * High-water marks for the delta sync. One row per (peer, table, direction)
 * records how far we have synced, so each cycle only moves rows changed since
 * the last successful run instead of rescanning everything.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sync_states', function (Blueprint $table) {
            $table->id();
            $table->string('peer');                       // e.g. 'cpanel' or 'local'
            $table->string('table_name');                 // e.g. 'orders'
            $table->enum('direction', ['pull', 'push']);
            $table->timestamp('last_synced_at')->nullable();   // watermark on updated_at
            $table->string('last_cursor')->nullable();         // tie-breaker (ulid) at the watermark
            $table->timestamps();

            $table->unique(['peer', 'table_name', 'direction']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sync_states');
    }
};
