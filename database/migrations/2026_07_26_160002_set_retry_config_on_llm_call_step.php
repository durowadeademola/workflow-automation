<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * llm_call is currently the only step in either seeded workflow that
     * actually throws on failure (every other step catches its own errors
     * and degrades gracefully) — so it's the one place retries are both
     * safe (an LLM call has no side effect to double) and actually useful
     * (transient rate-limit/timeout errors are common and worth one retry).
     */
    public function up(): void
    {
        DB::table('automation_workflow_steps')
            ->where('type', 'llm_call')
            ->update(['max_attempts' => 2, 'retry_delay_ms' => 750]);
    }

    public function down(): void
    {
        DB::table('automation_workflow_steps')
            ->where('type', 'llm_call')
            ->update(['max_attempts' => 1, 'retry_delay_ms' => 500]);
    }
};
