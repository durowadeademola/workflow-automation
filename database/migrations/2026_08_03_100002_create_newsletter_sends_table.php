<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * One row per actual recipient of a Newsletter — gives every send its own
 * unguessable tracking_token for the unsubscribe link, without exposing a
 * real Customer/NewsletterSubscriber id in the URL. `subscriber_type` +
 * `subscriber_id` is a lightweight polymorphic pair (not Eloquent's
 * morphTo, since exactly two fixed audiences exist) so the same unsubscribe
 * route can flip either Customer.subscribed_to_marketing or
 * NewsletterSubscriber.subscribed depending which table the token belongs to.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_id')->constrained()->cascadeOnDelete();
            $table->string('subscriber_type'); // 'customer' | 'subscriber'
            $table->unsignedBigInteger('subscriber_id');
            $table->string('tracking_token')->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['subscriber_type', 'subscriber_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_sends');
    }
};
