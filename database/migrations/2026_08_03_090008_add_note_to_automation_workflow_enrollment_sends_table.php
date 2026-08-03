<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_workflow_enrollment_sends', function (Blueprint $table) {
            // Why a send was skipped/failed (e.g. "channel_not_configured",
            // "limit_reached", "unsubscribed") — status alone doesn't say
            // why, and this matters for debugging a client's journey.
            $table->string('note')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('automation_workflow_enrollment_sends', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
