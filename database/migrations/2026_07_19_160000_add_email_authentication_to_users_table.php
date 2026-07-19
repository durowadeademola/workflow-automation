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
            // Unlike app-based 2FA, there's no secret to store here — the
            // login/setup code itself is only ever kept transiently in the
            // session by Filament's EmailAuthentication provider. This is
            // just the on/off flag.
            $table->boolean('has_email_authentication')->default(false)->after('app_authentication_recovery_codes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('has_email_authentication');
        });
    }
};
