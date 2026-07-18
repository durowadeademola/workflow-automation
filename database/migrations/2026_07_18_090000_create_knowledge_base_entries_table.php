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
        Schema::create('knowledge_base_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            // faq (question + answer) or article (title + free-form content) —
            // both are handed to the AI the same way, this only affects labels.
            $table->string('type')->default('faq');
            $table->string('title');
            $table->text('content');
            // Kept (not sent to the AI) rather than deleted, so a client can
            // temporarily pull an entry without losing the writing.
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_entries');
    }
};
