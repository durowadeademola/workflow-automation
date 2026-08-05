<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The registration form already requires checking a "Terms of Service"
 * box (validated as `accepted` in ClientRegistrationController), but the
 * acceptance itself was never persisted — there was no durable record of
 * *when* a given client agreed. This doesn't replace the checkbox/clickwrap
 * flow (which is a legally standard, sufficient mechanism on its own); it
 * just makes that acceptance provable later.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('is_terms_accepted')->default(false);
            $table->timestamp('terms_accepted_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['is_terms_accepted', 'terms_accepted_at']);
        });
    }
};
