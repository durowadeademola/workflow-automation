<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Per-client, not a global switch — lets clients be moved onto
            // the native AutomationWorkflow engine one at a time while
            // everyone else keeps running on n8n exactly as before.
            $table->string('chat_engine')->default('n8n')->after('webhook_url');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('chat_engine');
        });
    }
};
