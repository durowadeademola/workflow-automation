<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_workflow_steps', function (Blueprint $table) {
            // Default 1 = no retry, matching every step's current behaviour
            // exactly — retrying is opt-in per step, not automatic, since a
            // blind retry on a non-idempotent step (e.g. one that books an
            // appointment) could double the side effect if the first
            // attempt actually succeeded server-side but the response was
            // lost. Only turn this up for steps you've judged safe to repeat.
            $table->unsignedTinyInteger('max_attempts')->default(1)->after('canvas_position');
            $table->unsignedInteger('retry_delay_ms')->default(500)->after('max_attempts');
        });
    }

    public function down(): void
    {
        Schema::table('automation_workflow_steps', function (Blueprint $table) {
            $table->dropColumn(['max_attempts', 'retry_delay_ms']);
        });
    }
};
