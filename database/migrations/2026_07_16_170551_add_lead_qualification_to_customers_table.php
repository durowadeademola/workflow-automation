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
        Schema::table('customers', function (Blueprint $table) {
            $table->boolean('is_qualified_lead')->default(false)->after('status');
            $table->string('lead_intent', 500)->nullable()->after('is_qualified_lead');
            $table->string('lead_budget')->nullable()->after('lead_intent');
            $table->string('lead_timeline')->nullable()->after('lead_budget');
            $table->timestamp('qualified_at')->nullable()->after('lead_timeline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['is_qualified_lead', 'lead_intent', 'lead_budget', 'lead_timeline', 'qualified_at']);
        });
    }
};
