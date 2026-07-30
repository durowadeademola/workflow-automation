<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Null = unlimited, same convention as appointment_limit/lead_limit.
            // FAQs answer instantly from the database with no AI/n8n call
            // involved (see WidgetFaqController), so without a cap a client
            // could stuff in unlimited free-form content and mostly avoid
            // ever using the paid AI conversation at all.
            $table->unsignedInteger('faq_limit')->nullable()->after('lead_limit');
        });

        DB::table('plans')->where('slug', 'chat-widget-starter')->update(['faq_limit' => 10]);
        DB::table('plans')->where('slug', 'chat-widget-professional')->update(['faq_limit' => 30]);
        // Enterprise stays null (unlimited) — no update needed.
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('faq_limit');
        });
    }
};
