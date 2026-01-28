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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('agent_id')->nullable()->constrained('agents');
            $table->foreignId('item_id')->nullable()->constrained('products');
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->integer('chat_id')->nullable();
            $table->string('state')->nullable();
            $table->text('message')->nullable();
            $table->string('platform')->nullable();
            $table->string('product')->nullable();
            $table->string('specs')->nullable();
            $table->string('assigned_agent')->nullable();
            $table->string('agent_email')->nullable();
            $table->string('status')->nullable();        
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
