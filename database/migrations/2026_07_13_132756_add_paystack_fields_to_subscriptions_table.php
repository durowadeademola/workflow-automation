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
            $table->string('plan')->nullable()->after('client_id'); // starter | professional | enterprise
            $table->unsignedInteger('amount')->nullable()->after('plan'); // Naira
            $table->string('status')->default('pending')->after('is_active'); // pending | active | expired | cancelled
            $table->string('paystack_reference')->nullable()->unique()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn(['plan', 'amount', 'status', 'paystack_reference']);
        });
    }
};
