<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The native "website-crawler" workflow (see
 * 2026_07_26_150000_seed_website_crawler_workflow.php) was already
 * parameterized per-client via {{trigger.clientId}}/{{trigger.websiteUrl}}/
 * {{trigger.paths}} placeholders, but nothing in the app ever actually
 * supplied those values for a real client — there was no field to even
 * store a client's website URL. This is what the new "Recrawl my site"
 * action on WidgetSettings reads from to build that trigger payload.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('website_url')->nullable()->after('webhook_url');
            $table->json('crawl_paths')->nullable()->after('website_url');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['website_url', 'crawl_paths']);
        });
    }
};
