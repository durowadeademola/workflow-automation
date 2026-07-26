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
        return [
            'pagesFetched' => count($context->get('steps.fetch.pages', [])),
            'chunksExtracted' => count($context->get('steps.chunk.chunks', [])),
            'chunksEmbedded' => count($context->get('steps.embed.embedded', [])),
            'chunksStored' => $context->get('steps.store.stored', 0),
            'chunksFailed' => $context->get('steps.store.failed', 0),
        ];
    }
}
