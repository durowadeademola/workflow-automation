<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // A single customer's stateful progress through one journey
        // (AutomationWorkflow). Distinct from automation_workflow_runs,
        // which is a one-shot, synchronous, all-steps-now execution record
        // for the chat-widget-reply/crawler workflows — journeys need to
        // persist across days between steps, which that model doesn't
        // support and isn't changed to support here.
        Schema::create('automation_workflow_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_id')->constrained()->cascadeOnDelete();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('active'); // active|completed|exited|failed
            $table->unsignedInteger('current_step_order')->default(0);

            // Null = due right now (picked up by the very next
            // AdvanceMarketingJourneys tick). Set = wait until this instant.
            $table->timestamp('next_run_at')->nullable();

            // Accumulated step outputs across ticks — each tick is a
            // separate PHP invocation (unlike WorkflowContext, which only
            // ever lives in memory for one synchronous run), so anything a
            // later step's {{...}} templating needs from an earlier one
            // must be persisted here in between.
            $table->json('context')->nullable();

            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('exited_at')->nullable();
            $table->string('exit_reason')->nullable();

            $table->timestamps();

            // Enforced in code (AdvanceMarketingJourneys / the enroll
            // action), not a DB constraint — a customer can be re-enrolled
            // later once a prior enrollment has actually finished, so a
            // unique index on (workflow, customer) alone would be wrong.
            $table->index(['automation_workflow_id', 'customer_id', 'status'], 'awe_workflow_customer_status_idx');
            $table->index(['status', 'next_run_at'], 'awe_status_next_run_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_enrollments');
    }
};
