<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per actual send attempt for one step of one enrollment —
        // gives per-step, per-channel analytics (open/click rate) without
        // growing an ever-larger json blob on the enrollment row itself.
        Schema::create('automation_workflow_enrollment_sends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enrollment_id')
                ->constrained('automation_workflow_enrollments')
                ->cascadeOnDelete();
            $table->string('step_key');
            $table->string('channel'); // email|whatsapp|sms|telegram
            $table->string('status'); // sent|skipped|failed
            $table->string('tracking_token')->unique();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_enrollment_sends');
    }
};
