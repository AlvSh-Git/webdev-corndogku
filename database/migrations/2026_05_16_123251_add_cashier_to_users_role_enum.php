<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MODIFY COLUMN (MySQL/MariaDB syntax) is not supported by SQLite.
        // SQLite enforces no column-level ENUM constraints anyway, so this
        // migration is intentionally skipped when running on SQLite (e.g. tests).
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','employee','cashier','customer') NOT NULL DEFAULT 'customer'");
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('owner','employee','customer') NOT NULL DEFAULT 'customer'");
    }
};
