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
            // 'monthly' or 'yearly' — decides both the period length
            // SubscriptionService::activateFree() sets on end_date, and the
            // x12 multiplier Client's limit methods apply to a yearly
            // period's message/appointment/lead caps.
            $table->string('billing_cycle')->default('monthly')->after('plan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('billing_cycle');
        });
    }
};
