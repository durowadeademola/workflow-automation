<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_workflow_steps', function (Blueprint $table) {
            // {"x": 120, "y": 340} — purely visual, for the workflow studio
            // canvas. Execution order still comes from `order`, not this.
            $table->json('canvas_position')->nullable()->after('run_if');
        });
    }

    public function down(): void
    {
        Schema::table('automation_workflow_steps', function (Blueprint $table) {
            $table->dropColumn('canvas_position');
        });
    }
};
