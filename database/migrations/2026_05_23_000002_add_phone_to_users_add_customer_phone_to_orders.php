<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Dedicated phone column — separate from username which doubles as phone for walk-in
            $table->string('phone', 20)->nullable()->after('email');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Stores the WA-target number at order creation time so receipts work even
            // if the user record changes or the order is for a walk-in without an account.
            $table->string('customer_phone', 20)->nullable()->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('customer_phone');
        });
    }
};
