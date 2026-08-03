<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            // Null on existing rows means "legacy, implicitly chat-widget" —
            // Chat Widget was the only sellable service when those were
            // created, so Client::currentSubscription()/hasActiveSubscription()
            // treat service=null the same as service='chat-widget'. Every new
            // subscription (trial or paid, any service) sets this explicitly,
            // which is what makes it possible for a client to hold two
            // independent, concurrently-billed subscriptions (e.g. Chat
            // Widget + Marketing Automation) without one clobbering the
            // other's limit checks.
            $table->string('service')->nullable()->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropColumn('service');
        });
    }
};
