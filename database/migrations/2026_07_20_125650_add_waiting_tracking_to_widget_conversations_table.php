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
        Schema::table('widget_conversations', function (Blueprint $table) {
            // Reset every time a conversation (re-)enters 'waiting' — distinct
            // from created_at, which stays fixed even when an old, closed
            // conversation is reopened for a new handoff request.
            $table->timestamp('waiting_since')->nullable();
            // Set once the no-response nudge fires, so it's never repeated
            // on every subsequent poll.
            $table->timestamp('nudge_sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('widget_conversations', function (Blueprint $table) {
            $table->dropColumn(['waiting_since', 'nudge_sent_at']);
        });
    }
};
