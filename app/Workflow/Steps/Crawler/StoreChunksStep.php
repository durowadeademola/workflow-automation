<?php

namespace App\Workflow\Steps\Crawler;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Prepare for Supabase" + "Store in Supabase" nodes — inserts
 * each embedded chunk as its own row via Supabase's REST API, same table
 * the embedding server's /search endpoint (via match_website_chunks) reads
 * from at chat time.
 */
class StoreChunksStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $embedded = $context->get('steps.embed.embedded', []);
        $baseUrl = rtrim(config('services.supabase.url'), '/');
        $key = config('services.supabase.key');

        $stored = 0;
        $failed = 0;

        foreach ($embedded as $item) {
            try {
                $response = Http::withHeaders([
                    'apikey' => $key,
                    'Authorization' => "Bearer {$key}",
                    'Content-Type' => 'application/json',
                    'Prefer' => 'return=minimal',
                ])->timeout(10)->post("{$baseUrl}/rest/v1/website_chunks", [
                    'client_id' => $item['clientId'],
                    'url' => $item['url'],
                    'content' => $item['content'],
                    'embedding' => $item['embeddingString'],
                    'metadata' => [
                        'chunkIndex' => $item['chunkIndex'],
                        'contentLength' => mb_strlen($item['content']),
                    ],
                ]);

                $response->successful() ? $stored++ : $failed++;
            } catch (\Throwable $e) {
                Log::warning('Crawler: store chunk failed', ['url' => $item['url'], 'error' => $e->getMessage()]);
                $failed++;
            }
        }

        return ['stored' => $stored, 'failed' => $failed];
    }
}
