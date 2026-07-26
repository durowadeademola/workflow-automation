<?php

namespace App\Workflow\Steps\ChatWidget;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Ports n8n's "Basic LLM Chain" node. n8n's own active model is OpenRouter's
 * meta-llama/llama-3.3-70b-instruct:free; this also supports Groq (n8n has a
 * disabled backup node for llama-3.3-70b-versatile) since GROQ_API_KEY is
 * already configured, so this step works today without waiting on a new key.
 */
class LlmCallStep implements StepHandler
{
    private const ENDPOINTS = [
        'groq' => 'https://api.groq.com/openai/v1/chat/completions',
        'openrouter' => 'https://openrouter.ai/api/v1/chat/completions',
    ];

    public function execute(array $config, WorkflowContext $context): array
    {
        $provider = $config['provider'] ?? 'groq';
        $endpoint = self::ENDPOINTS[$provider] ?? null;

        if (! $endpoint) {
            throw new RuntimeException("Unknown LLM provider [{$provider}].");
        }

        $apiKey = config("services.{$provider}.key");

        if (! $apiKey) {
            throw new RuntimeException("Missing API key for LLM provider [{$provider}] — set it in .env / config/services.php.");
        }

        $response = Http::timeout(30)
            ->withToken($apiKey)
            ->post($endpoint, [
                'model' => $config['model'],
                'max_tokens' => $config['max_tokens'] ?? 512,
                'temperature' => $config['temperature'] ?? 0.7,
                'messages' => [
                    ['role' => 'system', 'content' => $config['system_prompt'] ?? ''],
                    ['role' => 'user', 'content' => $config['user_message'] ?? ''],
                ],
            ]);

        if (! $response->successful()) {
            throw new RuntimeException("LLM call to [{$provider}] failed: " . $response->body());
        }

        $text = $response->json('choices.0.message.content');

        return ['text' => $text ?: 'Sorry, I could not respond right now.'];
    }
}
