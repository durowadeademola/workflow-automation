<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // Define a client_id to keep data consistent for one "business"
        $clientId = 1; 

        for ($month = 1; $month <= 12; $month++) {
            // Create a realistic "growth" count (more customers each month)
            $count = $month * rand(5, 10); 

            for ($i = 0; $i < $count; $i++) {
                // Generate a random day in that specific month of 2026
                $date = Carbon::create(2026, $month, rand(1, 28), rand(8, 20));

                // 1. Create Dummy Customer
                $customer = Customer::create([
                    'client_id' => $clientId,
                    'name' => "Customer " . Str::random(5),
                    'status' => 'active',
                    'platform' => 'WhatsApp',
                    'created_at' => $date,
                    'updated_at' => $date,
                ]);

                // 2. Create 1-2 Dummy Orders for some of these customers
                if (rand(0, 1)) {
                    Order::create([
                        'client_id' => $clientId,
                        'customer_id' => $customer->id,
                        'customer_name' => $customer->name,
                        'order_reference' => 'ORD-' . strtoupper(Str::random(8)),
                        'amount' => rand(5000, 50000), // Random Naira amount
                        'currency' => 'NGN',
                        'status' => 'completed',
                        'created_at' => $date->addHours(2), // Order happens shortly after lead
                        'updated_at' => $date,
                    ]);
                }
            }
        }
    }
}