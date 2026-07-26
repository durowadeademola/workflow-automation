<?php

namespace App\Workflow\Steps\Crawler;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Delete Old Chunks" node — clears this client's existing
 * website_chunks rows in Supabase before re-crawling, so a re-crawl never
 * leaves stale/duplicate chunks behind alongside fresh ones.
 */
class DeleteOldChunksStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $baseUrl = rtrim(config('services.supabase.url'), '/');
        $key = config('services.supabase.key');

        try {
            $response = Http::withHeaders([
                'apikey' => $key,
                'Authorization' => "Bearer {$key}",
                'Content-Type' => 'application/json',
            ])->timeout(10)->post("{$baseUrl}/rest/v1/rpc/delete_client_chunks", [
                'p_client_id' => $config['client_id'] ?? '',
            ]);

            return ['deleted' => $response->successful()];
        } catch (\Throwable $e) {
            Log::warning('Crawler: delete old chunks failed', ['error' => $e->getMessage()]);

            return ['deleted' => false];
        }
    }
}
