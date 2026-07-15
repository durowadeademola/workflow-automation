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
            // Null means "universal" — shown regardless of which service(s)
            // a client picked, same convention as clients.features being
            // null meaning unrestricted. A real value restricts the plan to
            // clients who selected that specific service.
            $table->string('service')->nullable()->after('slug');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn('service');
        });
    }
};
