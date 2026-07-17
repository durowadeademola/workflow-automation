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
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('promo_price')->nullable()->after('amount');
            // Optional — leave blank for a promo that runs until manually
            // cleared, or set it for one that stops showing on its own.
            $table->timestamp('promo_ends_at')->nullable()->after('promo_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['promo_price', 'promo_ends_at']);
        });
    }
};
