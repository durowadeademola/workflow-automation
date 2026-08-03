<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // No existing opt-out concept on this table — required for a
            // one-click, no-login-needed unsubscribe link in marketing
            // journey emails/messages.
            $table->boolean('subscribed_to_marketing')->default(true)->after('registered_at');
            $table->timestamp('unsubscribed_at')->nullable()->after('subscribed_to_marketing');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['subscribed_to_marketing', 'unsubscribed_at']);
        });
    }
};
