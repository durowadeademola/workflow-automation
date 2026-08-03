<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_workflow_steps', function (Blueprint $table) {
            // How long to wait, after the PREVIOUS step completes, before
            // this one runs — the existing engine (WorkflowExecutor) has no
            // notion of this at all; it runs every step of a workflow
            // synchronously in one pass. Marketing journeys are advanced
            // instead by AdvanceMarketingJourneys, which reads these to
            // compute an enrollment's next_run_at. 0/'hours' (the defaults)
            // means "run immediately", matching the existing engine's
            // always-immediate behaviour for chat-widget-reply/crawler steps.
            $table->unsignedInteger('wait_amount')->default(0)->after('order');
            $table->string('wait_unit')->default('hours')->after('wait_amount');

            // Which channel a marketing send-step goes out on. Null for
            // every existing chat-widget/crawler step type, which don't send
            // anything on an external channel.
            $table->string('channel')->nullable()->after('wait_unit');

            // Same {"field": ..., "equals": ...} shape as the existing
            // run_if, but the opposite meaning: run_if says "skip just this
            // step", exit_if says "stop the whole journey here" (e.g. "the
            // appointment reminder journey should stop once the customer
            // actually books"). Checked by AdvanceMarketingJourneys via the
            // same tiny helper WorkflowExecutor::shouldRun() already uses.
            $table->json('exit_if')->nullable()->after('run_if');
        });
    }

    public function down(): void
    {
        Schema::table('automation_workflow_steps', function (Blueprint $table) {
            $table->dropColumn(['wait_amount', 'wait_unit', 'channel', 'exit_if']);
        });
    }
};
