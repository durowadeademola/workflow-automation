<?php

namespace App\Http\Controllers\API;

use App\Models\AIAgent;
use Illuminate\Http\Request;

class AIAgentController extends Controller
{
    public function insights(Request $request)
    {
        $agent = AIAgent::create([
            'customer_id' => $request->customer_id,
            'client_id' => $request->client_id,
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'service_id' => $request->service_id,
            'source' => $request->source,
            'model' => 'groq',
            'prompt' => $request->prompt,
            'response' => $request->response,
            'success' => $request->success ?? true,
            'latency' => $request->latency,
            'metadata' => [
                'workflow_id' => $request->workflow_id,
                'execution_id' => $request->execution_id,
                'node_name' => $request->node_name,
                'score' => $request->score,
                'priority' => $request->priority,
            ],
        ]);

        return response()->json($agent);
    }
}
