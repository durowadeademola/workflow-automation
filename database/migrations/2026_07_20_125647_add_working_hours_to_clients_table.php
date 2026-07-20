<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Off by default — an existing client who never configures this
            // keeps today's behavior (AI can hand off to an agent any time).
            $table->boolean('working_hours_enabled')->default(false);
            $table->json('working_days')->nullable();
            $table->time('working_hours_start')->nullable();
            $table->time('working_hours_end')->nullable();
            $table->string('timezone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'working_hours_enabled',
                'working_days',
                'working_hours_start',
                'working_hours_end',
                'timezone',
            ]);
        });
    }
};
