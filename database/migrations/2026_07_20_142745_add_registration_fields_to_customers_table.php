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
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            // Distinct from qualified_at (lead qualification is the AI's own
            // passive read on intent) — this marks an explicit "register my
            // details" submission via the widget's quick reply.
            $table->timestamp('registered_at')->nullable()->after('qualified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['email', 'phone', 'registered_at']);
        });
    }
};
