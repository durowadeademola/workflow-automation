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
            // Captured from Paystack's verify/webhook response at the moment
            // of activation, so an admin can audit "what did Paystack
            // actually record" in-app instead of switching to the Paystack
            // dashboard. Null for subscriptions activated with no real
            // charge (credit-covered switches, the free trial).
            $table->unsignedBigInteger('paystack_transaction_id')->nullable()->after('paystack_reference');
            $table->unsignedInteger('paystack_amount_charged')->nullable()->after('paystack_transaction_id');
            $table->string('paystack_channel')->nullable()->after('paystack_amount_charged');
            $table->timestamp('paystack_paid_at')->nullable()->after('paystack_channel');
            $table->string('paystack_gateway_response')->nullable()->after('paystack_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'paystack_transaction_id',
                'paystack_amount_charged',
                'paystack_channel',
                'paystack_paid_at',
                'paystack_gateway_response',
            ]);
        });
    }
};
