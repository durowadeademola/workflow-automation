<?php

namespace App\Workflow\Steps\Crawler;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Ports n8n's "Client Config" + "Fetch Page" nodes — but parameterized by
 * whatever's in the trigger payload instead of hardcoded per-client values,
 * so one workflow definition crawls every client instead of needing a
 * separate n8n copy (with its own hardcoded CLIENT_ID/WEBSITE_URL) per one.
 * Fetches proxy through the embedding server's own /fetch endpoint, same as
 * n8n did, since that's already set up to handle the actual HTTP GET.
 */
class FetchPagesStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $clientId = $config['client_id'] ?? '';
        $websiteUrl = rtrim($config['website_url'] ?? '', '/');
        $paths = $config['paths'] ?? [];
        $embeddingUrl = config('services.embedding_server.url');

        $pages = [];

        foreach ($paths as $path) {
            $fullUrl = $websiteUrl . '/' . ltrim($path, '/');

            try {
                $response = Http::timeout(15)->post("{$embeddingUrl}/fetch", [
                    'url' => $fullUrl,
                    'clientId' => $clientId,
                ]);

                $data = $response->json() ?? [];

                $pages[] = [
                    'url' => $fullUrl,
                    'html' => $data['data'] ?? '',
                    'success' => $data['success'] ?? false,
                    'error' => $data['error'] ?? null,
                    'statusCode' => $data['statusCode'] ?? null,
                ];
            } catch (\Throwable $e) {
                Log::warning('Crawler: fetch page failed', ['url' => $fullUrl, 'error' => $e->getMessage()]);

                $pages[] = ['url' => $fullUrl, 'html' => '', 'success' => false, 'error' => $e->getMessage()];
            }
        }

        return ['pages' => $pages];
    }
}
