<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Prefixed "automation_" to avoid colliding with the pre-existing,
        // unrelated `workflows` table (App\Models\Workflow — a client-facing
        // record with no engine behind it). This is the native execution
        // engine's own schema: definitions + steps + run history.
        Schema::create('automation_workflows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('trigger_type')->default('manual');
            $table->json('trigger_config')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_workflows');
    }
};
