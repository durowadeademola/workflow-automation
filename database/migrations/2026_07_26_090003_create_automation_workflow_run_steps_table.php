<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_workflow_run_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_run_id')->constrained()->cascadeOnDelete();
            // Explicit short constraint name — the auto-generated one is 65
            // chars, one over MySQL's identifier limit.
            $table->foreignId('automation_workflow_step_id')
                ->constrained(indexName: 'awrs_step_id_foreign')
                ->cascadeOnDelete();
            $table->string('key');
            $table->string('status'); // skipped, completed, failed
            $table->json('input')->nullable();
            $table->json('output')->nullable();
            $table->text('error')->nullable();
            $table->unsignedInteger('duration_ms')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_run_steps');
    }
};
