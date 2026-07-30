<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('knowledge_base_entries', function (Blueprint $table) {
            // Only ever mutated via WidgetFaqController::recordView()'s
            // atomic increment() — deliberately left out of the model's
            // $fillable so it can never be set directly from the admin/
            // client edit form.
            $table->unsignedInteger('views')->default(0)->after('sort_order');
        });
    }

    public function down(): void
    {
        Schema::table('knowledge_base_entries', function (Blueprint $table) {
            $table->dropColumn('views');
        });
    }
};
