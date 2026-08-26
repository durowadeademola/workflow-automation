<?php

namespace App\Workflow\Steps\Crawler;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * Always-run final step — n8n has no equivalent node (its execution log
 * serves this purpose), but the native engine has no visual per-run summary
 * yet, so this gives Workflow Studio's test-run panel and
 * `workflows:runs --run=` something readable to show instead of just the
 * last raw step output.
 */
class CrawlSummaryStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $pages = $context->get('steps.fetch.pages', []);
        $pageChunkCounts = $context->get('steps.chunk.pageChunkCounts', []);

        // Per-page breakdown so a client can actually see WHAT went wrong
        // and WHERE, instead of just an aggregate "0 chunks indexed" that
        // gives no clue whether a page 404'd, failed to fetch entirely, or
        // fetched fine but had no extractable text (e.g. a JS-rendered page
        // with nothing in the raw HTML).
        $pageResults = array_map(function ($page) use ($pageChunkCounts) {
            $chunkCount = $pageChunkCounts[$page['url']] ?? 0;

            if (! ($page['success'] ?? false)) {
                $status = 'fetch_failed';
                $message = $this->friendlyFetchError($page['statusCode'] ?? null, $page['error'] ?? null);
            } elseif ($chunkCount === 0) {
                $status = 'no_content';
                $message = 'Page loaded, but no usable text was found (it may require JavaScript to render, or be too short).';
            } else {
                $status = 'indexed';
                $message = "{$chunkCount} chunk(s) indexed.";
            }

            return [
                'url' => $page['url'],
                'status' => $status,
                'message' => $message,
                'chunksIndexed' => $chunkCount,
            ];
        }, $pages);

        return [
            'pagesFetched' => count($pages),
            'chunksExtracted' => count($context->get('steps.chunk.chunks', [])),
            'chunksEmbedded' => count($context->get('steps.embed.embedded', [])),
            'chunksStored' => $context->get('steps.store.stored', 0),
            'chunksFailed' => $context->get('steps.store.failed', 0),
            'pageResults' => $pageResults,
        ];
    }

    /**
     * "HTTP 404" is accurate but means nothing to a client who isn't a
     * developer — this translates the status codes actually worth calling
     * out by name into plain language, and falls back to the raw error
     * (e.g. a connection failure message) for anything else rather than
     * inventing a vague message that hides real detail.
     */
    private function friendlyFetchError(?int $statusCode, ?string $rawError): string
    {
        $reason = match ($statusCode) {
            404 => "This page wasn't found on your site",
            403 => 'Access to this page was denied',
            401 => 'This page requires you to be logged in to view it',
            500, 502, 503, 504 => 'This page returned a server error',
            default => null,
        };

        if ($reason === null) {
            return $rawError ?? 'Could not fetch this page.';
        }

        return $statusCode ? "{$reason} (HTTP {$statusCode})." : "{$reason}.";
    }
}
