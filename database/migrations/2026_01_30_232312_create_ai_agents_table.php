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
        Schema::create('ai_agents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('product_id')->nullable()->constrained('products');
            $table->foreignId('service_id')->nullable()->constrained('services');
            $table->string('source')->default('external'); // n8n, api, dashboard
            $table->string('model')->nullable(); // llama3 (optional but useful)
            $table->longText('prompt')->nullable();
            $table->longText('response')->nullable();
            $table->boolean('success')->default(true);
            $table->text('error')->nullable();
            $table->float('latency')->nullable(); // seconds
            $table->json('metadata')->nullable(); // workflow_id, execution_id, node_name
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ollama_a_i_s');
    }
};
