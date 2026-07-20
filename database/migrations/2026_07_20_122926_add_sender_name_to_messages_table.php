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
        Schema::table('messages', function (Blueprint $table) {
            // Only ever set for human agent replies — AI replies leave this
            // null so restored widget history displays whatever the
            // client's assistant is CURRENTLY named, rather than freezing
            // whatever it was called at the time the message was sent.
            $table->string('sender_name')->nullable()->after('from_customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('sender_name');
        });
    }
};
