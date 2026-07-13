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
        Schema::create('widget_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('widget_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('sender_type'); // visitor | ai | agent
            $table->string('sender_name')->nullable();
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_messages');
    }
};
