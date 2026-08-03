<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Blueflow's own agency-level newsletter audience — visitors to the public
 * marketing site who sign up, distinct from a client's Customer list (which
 * already has its own subscribed_to_marketing opt-out for Marketing
 * Automation journeys/newsletters). See Newsletter/NewsletterSend, which
 * treat this table and Customer as two interchangeable recipient sources.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->unique();
            $table->boolean('subscribed')->default(true);
            $table->timestamp('subscribed_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
    }
};
