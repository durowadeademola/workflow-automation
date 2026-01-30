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
        Schema::table('agents', function (Blueprint $table) {
            // This adds the unique constraint to the email column
            $table->string('email')->unique()->change();
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // This drops the unique constraint if you roll back
            $table->dropUnique(['email']);
        });
    }
};
