<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AIAgent;
use Illuminate\Http\Request;

class AIAgentController extends Controller
{
    public function insights(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'order_id' => ['nullable', 'exists:orders,id'],
            'product_id' => ['nullable', 'exists:products,id'],
            'service_id' => ['nullable', 'exists:services,id'],
            'source' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'prompt' => ['nullable', 'string'],
            'text' => ['nullable', 'string'],
            'response' => ['nullable', 'string'],
            'score' => ['nullable'],
            'priority' => ['nullable'],
        ]);

        // Log the request to storage/logs/laravel.log to see exactly what n8n is sending
        \Log::info('AI Logs Data:', $validated);

        $agent = AIAgent::create([
            'customer_id' => $validated['customer_id'] ?? null,
            'client_id' => $validated['client_id'],
            'order_id' => $validated['order_id'] ?? null,
            'product_id' => $validated['product_id'] ?? null,
            'service_id' => $validated['service_id'] ?? null,
            'source' => $validated['source'] ?? null,
            'model' => $validated['model'] ?? 'Groq-Llama-3.3',
            'prompt' => $validated['prompt'] ?? $validated['text'] ?? null,
            'response' => $validated['response'] ?? null,
            'success' => true,
            'metadata' => [
                'score' => $validated['score'] ?? null,
                'priority' => $validated['priority'] ?? null,
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $agent,
        ]);
    }
}
