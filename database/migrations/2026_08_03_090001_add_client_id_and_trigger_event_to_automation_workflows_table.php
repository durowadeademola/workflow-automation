<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_workflows', function (Blueprint $table) {
            // Null = a shared, client-agnostic system workflow (e.g. the
            // seeded chat-widget-reply / website-crawler workflows, which
            // serve every client through their own trigger payload). Set =
            // a specific client's own workflow — Marketing Journeys are the
            // first feature to actually populate this.
            $table->foreignId('client_id')->nullable()->after('id')->constrained()->cascadeOnDelete();

            // Which behaviour auto-enrolls a customer into this journey.
            // Null means manual-only (enrolled via the "Enroll a segment"
            // Filament action, never automatically). The existing
            // trigger_config json column holds the per-trigger tuning value
            // (e.g. {"hours": 24} for abandoned_booking, {"days": 30} for
            // re_engagement) — reused rather than duplicated.
            $table->string('trigger_event')->nullable()->after('trigger_type');
        });
    }

    public function down(): void
    {
        Schema::table('automation_workflows', function (Blueprint $table) {
            $table->dropConstrainedForeignId('client_id');
            $table->dropColumn('trigger_event');
        });
    }
};
