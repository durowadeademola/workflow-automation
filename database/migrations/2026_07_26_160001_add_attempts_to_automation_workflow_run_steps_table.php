<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('automation_workflow_run_steps', function (Blueprint $table) {
            $table->unsignedTinyInteger('attempts')->default(1)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('automation_workflow_run_steps', function (Blueprint $table) {
            $table->dropColumn('attempts');
        });
    }
};
