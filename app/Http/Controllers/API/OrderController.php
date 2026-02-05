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
        // Log the request to storage/logs/laravel.log to see exactly what n8n is sending
        \Log::info('Order Logs Data:', $request->all());

        $order_id = Str::upper(Str::random(7));

        $order = Order::create([
            'client_id' => $request->client_id,
            'customer_id' => $request->customer_id,
            'agent_id' => null,
            'product_id' => $request->product_id,
            'service_id' => $request->service_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'order_reference' => $order_id,
            //'amount' => $request->amount ?? '200,000',
            //'currency' => $request->currency ?? 'NGN',
            'status' => 'new',
            'source' => $request->source ?? 'Telegram',
            'notes' => $request->specs,
        ]);

        $agent = Agent::where('client_id', $request->client_id)
            ->withCount(['orders' => function ($q) {
                $q->where('status', 'new');
            }])
            ->orderBy('orders_count')
            ->first();

        $order->update([
            'agent_id' => $agent->id,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $order,
        ]);
    }
}
