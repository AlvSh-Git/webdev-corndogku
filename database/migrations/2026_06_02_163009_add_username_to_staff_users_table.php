<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_users', function (Blueprint $table) {
            if (!Schema::hasColumn('staff_users', 'name')) {
                $table->string('name')->after('id');
            }

            if (!Schema::hasColumn('staff_users', 'username')) {
                $table->string('username')->unique()->after('name');
            }

            if (!Schema::hasColumn('staff_users', 'role')) {
                $table->string('role')->default('Employee')->after('username');
            }

            if (!Schema::hasColumn('staff_users', 'position')) {
                $table->string('position')->nullable()->after('role');
            }

            if (!Schema::hasColumn('staff_users', 'branch')) {
                $table->string('branch')->nullable()->after('position');
            }

            if (!Schema::hasColumn('staff_users', 'phone')) {
                $table->string('phone')->nullable()->after('branch');
            }

            if (!Schema::hasColumn('staff_users', 'password')) {
                $table->string('password')->after('phone');
            }

            if (!Schema::hasColumn('staff_users', 'active')) {
                $table->boolean('active')->default(true)->after('password');
            }
        });
    }

    public function down(): void
    {
        Schema::table('staff_users', function (Blueprint $table) {
            if (Schema::hasColumn('staff_users', 'active')) {
                $table->dropColumn('active');
            }

            if (Schema::hasColumn('staff_users', 'password')) {
                $table->dropColumn('password');
            }

            if (Schema::hasColumn('staff_users', 'phone')) {
                $table->dropColumn('phone');
            }

            if (Schema::hasColumn('staff_users', 'branch')) {
                $table->dropColumn('branch');
            }

            if (Schema::hasColumn('staff_users', 'position')) {
                $table->dropColumn('position');
            }

            if (Schema::hasColumn('staff_users', 'role')) {
                $table->dropColumn('role');
            }

            if (Schema::hasColumn('staff_users', 'username')) {
                $table->dropColumn('username');
            }

            if (Schema::hasColumn('staff_users', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
