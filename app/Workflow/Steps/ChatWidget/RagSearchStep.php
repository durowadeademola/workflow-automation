<?php

namespace App\Workflow\Steps\ChatWidget;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Embed and Search" node: queries the Flask embedding server
 * for website content chunks relevant to the visitor's message.
 */
class RagSearchStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $baseUrl = config('services.embedding_server.url', 'http://127.0.0.1:5000');

        try {
            $response = Http::timeout(10)->post("{$baseUrl}/search", [
                'text' => $config['text'] ?? '',
                'client_id' => $config['client_id'] ?? null,
                'threshold' => 0.3,
                'count' => 5,
            ]);

            return ['results' => $response->successful() ? ($response->json('results') ?? []) : []];
        } catch (\Throwable $e) {
            Log::warning('Native workflow: RAG search failed', ['error' => $e->getMessage()]);

            return ['results' => []];
        }
    }
}
