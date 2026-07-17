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
            // requested -> rejected|processed. Null means no refund was ever requested.
            $table->string('refund_status')->nullable()->after('cancellation_reason');
            $table->unsignedInteger('refund_amount')->nullable()->after('refund_status');
            $table->timestamp('refund_requested_at')->nullable()->after('refund_amount');
            $table->timestamp('refund_reviewed_at')->nullable()->after('refund_requested_at');
            $table->timestamp('refund_processed_at')->nullable()->after('refund_reviewed_at');
            $table->string('refund_reference')->nullable()->after('refund_processed_at');
            $table->string('refund_rejection_reason')->nullable()->after('refund_reference');
            // The pre-refund-request end_date, preserved so access can be
            // restored if an admin rejects the request instead of processing it.
            $table->date('refund_original_end_date')->nullable()->after('refund_rejection_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn([
                'refund_status',
                'refund_amount',
                'refund_requested_at',
                'refund_reviewed_at',
                'refund_processed_at',
                'refund_reference',
                'refund_rejection_reason',
                'refund_original_end_date',
            ]);
        });
    }
};
