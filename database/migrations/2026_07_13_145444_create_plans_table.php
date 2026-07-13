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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedInteger('amount'); // Naira
            $table->string('description')->nullable();
            $table->json('features')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed the plans already in use across the marketing site and
        // checkout, so nothing breaks the moment this migration runs.
        DB::table('plans')->insert([
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'amount' => 20000,
                'description' => 'Perfect for small businesses just getting started with automation',
                'features' => json_encode([
                    'WhatsApp Business API',
                    'Up to 1,000 conversations/month',
                    'Basic automation flows',
                    'Email support',
                    'Mobile app access',
                    '2 team members',
                    'Standard templates',
                ]),
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'amount' => 35000,
                'description' => 'For growing businesses that need more power and flexibility',
                'features' => json_encode([
                    'Everything in Starter',
                    'Up to 5,000 conversations/month',
                    'Advanced automation + AI',
                    'CRM integration',
                    'Priority support (24/7)',
                    '5 team members',
                    'Custom workflows',
                    'Analytics dashboard',
                    'Payment processing',
                ]),
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'amount' => 60000,
                'description' => 'For established businesses with complex needs',
                'features' => json_encode([
                    'Everything in Professional',
                    'Unlimited conversations',
                    'Custom AI training',
                    'Dedicated account manager',
                    'White-label options',
                    'Unlimited team members',
                    'API access',
                    'Custom integrations',
                    'SLA guarantee',
                    'Advanced security',
                ]),
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
