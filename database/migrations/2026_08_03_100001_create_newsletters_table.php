<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A single one-off broadcast — distinct from AutomationWorkflow (drip
 * journeys with waits between steps); a newsletter sends once, to everyone
 * eligible, right away. `client_id` null means Blueflow's own agency
 * newsletter (recipients: NewsletterSubscriber); set means a client's own
 * broadcast to their Customers — same nullable-FK convention already used by
 * automation_workflows.client_id.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('subject');
            $table->longText('body_html');
            $table->string('status')->default('draft'); // draft|sending|sent
            $table->timestamp('sent_at')->nullable();
            $table->unsignedInteger('recipients_count')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletters');
    }
};
