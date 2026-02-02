<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AIAgent;
use Illuminate\Http\Request;

class AIAgentController extends Controller
{
    public function insights(Request $request)
    {
        // Log the request to storage/logs/laravel.log to see exactly what n8n is sending
        \Log::info('AI Logs Data:', $request->all());

        $agent = AIAgent::create([
            'customer_id' => $request->customer_id,
            'source' => $request->source,
            'model' => $request->model ?? 'Groq-Llama-3.3',
            'prompt' => $request->prompt ?? $request->text,
            'response' => $request->response,
            'success' => true,
            'metadata' => [
                'score' => $request->score,
                'priority' => $request->priority,
                // 'workflow_id' => $request->workflow_id,
                // 'node_name' => $request->node_name,
            ],
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $agent,
        ]);
    }
}
