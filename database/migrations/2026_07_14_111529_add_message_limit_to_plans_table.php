<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Null means unlimited.
            $table->unsignedInteger('message_limit')->nullable()->after('amount');
        });

        DB::table('plans')->where('slug', 'starter')->update(['message_limit' => 500]);
        DB::table('plans')->where('slug', 'professional')->update(['message_limit' => 2000]);
        // Enterprise stays unlimited (null).
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('message_limit');
        });
    }
};
