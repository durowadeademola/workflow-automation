<?php

namespace App\Workflow\Steps\Crawler;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * Ports n8n's "Extract & Chunk" code node — same HTML stripping, same
 * 500-char chunks with 50-char overlap, same length thresholds (skip pages
 * under 50 chars of text, skip chunks under 100 chars).
 */
class ExtractAndChunkStep implements StepHandler
{
    private const CHUNK_SIZE = 500;

    private const OVERLAP = 50;

    public function execute(array $config, WorkflowContext $context): array
    {
        $pages = $context->get('steps.fetch.pages', []);
        $chunks = [];

        foreach ($pages as $page) {
            $text = $this->stripHtml($page['html'] ?? '');

            if (mb_strlen($text) < 50) {
                continue;
            }

            $start = 0;
            $chunkIndex = 0;
            $length = mb_strlen($text);

            while ($start < $length) {
                $end = min($start + self::CHUNK_SIZE, $length);
                $chunk = trim(mb_substr($text, $start, $end - $start));

                if (mb_strlen($chunk) > 100) {
                    $chunks[] = [
                        'url' => $page['url'],
                        'content' => $chunk,
                        'chunkIndex' => $chunkIndex,
                    ];
                    $chunkIndex++;
                }

                $start += self::CHUNK_SIZE - self::OVERLAP;
            }
        }

        return ['chunks' => $chunks];
    }

    private function stripHtml(string $html): string
    {
        $text = preg_replace('/<script[^>]*>[\s\S]*?<\/script>/i', '', $html);
        $text = preg_replace('/<style[^>]*>[\s\S]*?<\/style>/i', '', $text);
        $text = preg_replace('/<nav[^>]*>[\s\S]*?<\/nav>/i', '', $text);
        $text = preg_replace('/<footer[^>]*>[\s\S]*?<\/footer>/i', '', $text);
        $text = preg_replace('/<header[^>]*>[\s\S]*?<\/header>/i', '', $text);
        $text = preg_replace('/<[^>]+>/', ' ', $text);
        $text = str_replace(['&nbsp;', '&amp;', '&lt;', '&gt;'], [' ', '&', '<', '>'], $text);
        $text = str_replace(['\\n', '\\t'], ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);

        return trim($text);
    }
}
