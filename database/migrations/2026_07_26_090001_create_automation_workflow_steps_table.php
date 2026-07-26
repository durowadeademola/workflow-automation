<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_workflow_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_workflow_id')->constrained()->cascadeOnDelete();
            // Unique per workflow, not globally — this is what later steps'
            // run_if/payload templates reference (e.g. "steps.extract.reply").
            $table->string('key');
            // Looked up in config('workflow.steps') to resolve the handler class.
            $table->string('type');
            $table->json('config')->nullable();
            // {"field": "steps.extract.wantsAppointment", "equals": true} — null
            // means the step always runs.
            $table->json('run_if')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['automation_workflow_id', 'key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_workflow_steps');
    }
};
