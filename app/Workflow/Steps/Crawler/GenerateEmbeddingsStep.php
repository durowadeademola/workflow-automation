<?php

namespace App\Workflow\Steps\Crawler;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Generate Embedding" + "Build Embedding" nodes — calls the
 * embedding server's /embed endpoint per chunk, then formats the resulting
 * vector as the "[1,2,3,...]" string Postgres/pgvector expects for storage.
 */
class GenerateEmbeddingsStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $clientId = $config['client_id'] ?? '';
        $chunks = $context->get('steps.chunk.chunks', []);
        $embeddingUrl = config('services.embedding_server.url');

        $embedded = [];

        foreach ($chunks as $chunk) {
            try {
                $response = Http::timeout(15)->post("{$embeddingUrl}/embed", [
                    'text' => $chunk['content'],
                ]);

                $embedding = $response->json('embedding') ?? [];

                if (empty($embedding)) {
                    continue;
                }

                $embedded[] = [
                    'clientId' => $clientId,
                    'url' => $chunk['url'],
                    'content' => $chunk['content'],
                    'chunkIndex' => $chunk['chunkIndex'],
                    'embeddingString' => '[' . implode(',', $embedding) . ']',
                ];
            } catch (\Throwable $e) {
                Log::warning('Crawler: embedding failed for chunk', ['url' => $chunk['url'], 'error' => $e->getMessage()]);
            }
        }

        return ['embedded' => $embedded];
    }
}
