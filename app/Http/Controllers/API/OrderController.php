<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'source' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        // Log the request to storage/logs/laravel.log to see exactly what n8n is sending
        \Log::info('Order Logs Data:', $validated);

        $order_id = Str::upper(Str::random(7));

        $agent = Agent::where('client_id', $validated['client_id'])
            ->withCount(['orders' => function ($q) {
                $q->where('status', 'new');
            }])
            ->orderBy('orders_count')
            ->first();

        $order = Order::create([
            'client_id' => $validated['client_id'],
            'customer_id' => $validated['customer_id'] ?? null,
            'agent_id' => $agent?->id,
            'product_id' => $validated['product_id'] ?? null,
            'service_id' => $validated['service_id'] ?? null,
            'customer_name' => $validated['customer_name'] ?? null,
            'customer_phone' => $validated['customer_phone'] ?? null,
            'customer_email' => $validated['customer_email'] ?? null,
            'order_reference' => $order_id,
            'status' => 'new',
            'source' => $validated['source'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }
}
