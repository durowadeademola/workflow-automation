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
        Schema::table('users', function (Blueprint $table) {
            // Both nullable/text — Filament's InteractsWithAppAuthentication[Recovery]
            // traits cast these as encrypted/encrypted:array, so the stored
            // value is an encrypted blob, not plain JSON/a short string.
            // Null secret means 2FA is off; set only once a user finishes
            // scanning the QR code and confirms a code (SetUpAppAuthenticationAction).
            $table->text('app_authentication_secret')->nullable()->after('email_notifications_enabled');
            $table->text('app_authentication_recovery_codes')->nullable()->after('app_authentication_secret');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['app_authentication_secret', 'app_authentication_recovery_codes']);
        });
    }
};
