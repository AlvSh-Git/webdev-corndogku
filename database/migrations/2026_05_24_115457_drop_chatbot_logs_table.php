<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('chatbot_logs');
    }

    public function down(): void
    {
        // Intentionally empty — the chatbot feature has been removed.
    }
};
