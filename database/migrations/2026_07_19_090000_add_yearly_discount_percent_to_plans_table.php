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
            // A standing discount for choosing annual billing over monthly —
            // distinct from promo_price/promo_ends_at, which is a time-boxed
            // promotional discount on the monthly price. Null/0 means no
            // yearly discount is offered (yearly still available at 12x the
            // monthly price, just with nothing struck through).
            $table->unsignedTinyInteger('yearly_discount_percent')->nullable()->after('promo_ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('yearly_discount_percent');
        });
    }
};
