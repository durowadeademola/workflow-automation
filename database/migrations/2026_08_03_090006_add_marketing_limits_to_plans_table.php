<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Same "null = unlimited" convention as appointment_limit/
            // lead_limit/faq_limit. Only meaningful on service =
            // 'marketing-automation' plans, but added generically like the
            // others rather than a separate table.
            $table->unsignedInteger('contact_limit')->nullable()->after('faq_limit');
            $table->unsignedInteger('journey_limit')->nullable()->after('contact_limit');
            $table->unsignedInteger('email_send_limit')->nullable()->after('journey_limit');
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['contact_limit', 'journey_limit', 'email_send_limit']);
        });
    }
};
