<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Placeholder pricing/limits — trivially editable afterward directly in
     * Filament (Plans resource), exactly like the existing chat-widget
     * plans. No code change is ever needed to adjust price or a limit.
     */
    public function up(): void
    {
        $now = now();

        DB::table('plans')->insert([
            [
                'name' => 'Starter',
                'slug' => 'marketing-automation-starter',
                'service' => 'marketing-automation',
                'amount' => 15000,
                'contact_limit' => 500,
                'journey_limit' => 3,
                'email_send_limit' => 2000,
                'description' => 'Perfect for small businesses just starting with automated customer journeys',
                'features' => json_encode([
                    'Email customer journeys',
                    'Behaviour-based triggers',
                    'Appointment & re-engagement journeys',
                    'Merge-field personalisation',
                ]),
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Professional',
                'slug' => 'marketing-automation-professional',
                'service' => 'marketing-automation',
                'amount' => 30000,
                'contact_limit' => 2500,
                'journey_limit' => 10,
                'email_send_limit' => 10000,
                'description' => 'For growing businesses running multiple customer journeys at once',
                'features' => json_encode([
                    'Everything in Starter',
                    'More active journeys and contacts',
                    'Higher monthly email volume',
                    'Priority support',
                ]),
                'is_popular' => true,
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'marketing-automation-enterprise',
                'service' => 'marketing-automation',
                'amount' => 55000,
                'contact_limit' => null,
                'journey_limit' => null,
                'email_send_limit' => null,
                'description' => 'For established businesses with high-volume customer journeys',
                'features' => json_encode([
                    'Everything in Professional',
                    'Unlimited contacts, journeys, and emails',
                    'Dedicated account manager',
                ]),
                'is_popular' => false,
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('plans')->where('service', 'marketing-automation')->delete();
    }
};
