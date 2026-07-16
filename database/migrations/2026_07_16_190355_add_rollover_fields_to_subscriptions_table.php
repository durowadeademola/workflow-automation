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
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedInteger('rolled_over_appointments')->default(0)->after('limit_reached_notified_at');
            $table->unsignedInteger('rolled_over_leads')->default(0)->after('rolled_over_appointments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['rolled_over_appointments', 'rolled_over_leads']);
        });
    }
};
