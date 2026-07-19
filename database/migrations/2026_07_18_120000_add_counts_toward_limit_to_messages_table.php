<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Defaults true so every existing row, and every other channel's
            // Message::create() call, is unaffected. Only the widget chat
            // proxy ever sets this to false — when it logs a visitor's
            // message but the AI never actually produced a reply (n8n
            // unreachable, or returned nothing usable), so that attempt
            // doesn't eat into the client's plan limit for nothing.
            $table->boolean('counts_toward_limit')->default(true)->after('from_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('counts_toward_limit');
        });
    }
};
