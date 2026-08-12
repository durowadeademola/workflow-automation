<?php

namespace App\Workflow\Steps\ChatWidget;

use App\Models\User;
use App\Notifications\LlmProviderFallbackTriggered;
use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use RuntimeException;
use Throwable;

/**
 * Ports n8n's "Basic LLM Chain" node. n8n's own active model is OpenRouter's
 * meta-llama/llama-3.3-70b-instruct:free; this also supports Groq (n8n has a
 * disabled backup node for llama-3.3-70b-versatile) since GROQ_API_KEY is
 * already configured, so this step works today without waiting on a new key.
 *
 * Both providers share one platform-wide API key each (not per-client), so
 * a rate limit, outage, or billing problem on the configured provider would
 * otherwise take down every client's chat widget at once. If the primary
 * call fails for any reason, this automatically retries once against the
 * other provider before giving up — turning "two supported providers" into
 * actual redundancy instead of just a config option nothing ever exercises.
 */
class LlmCallStep implements StepHandler
{
    private const ENDPOINTS = [
        'groq' => 'https://api.groq.com/openai/v1/chat/completions',
        'openrouter' => 'https://openrouter.ai/api/v1/chat/completions',
    ];

    /**
     * Groq and OpenRouter identify the same underlying model with different
     * strings (e.g. "llama-3.3-70b-versatile" vs
     * "meta-llama/llama-3.3-70b-instruct:free") — a fallback call can't just
     * reuse the primary's model string. This is only consulted when a
     * step's own config doesn't set an explicit fallback_provider/
     * fallback_model, so any workflow can still override it.
     */
    private const DEFAULT_FALLBACKS = [
        'groq' => ['provider' => 'openrouter', 'model' => 'meta-llama/llama-3.3-70b-instruct:free'],
        'openrouter' => ['provider' => 'groq', 'model' => 'llama-3.3-70b-versatile'],
    ];

    public function execute(array $config, WorkflowContext $context): array
    {
        $provider = $config['provider'] ?? 'groq';

        try {
            return $this->call($provider, $config['model'], $config);
        } catch (Throwable $primaryException) {
            $fallback = $this->resolveFallback($provider, $config);

            if (! $fallback) {
                throw $primaryException;
            }

            try {
                $result = $this->call($fallback['provider'], $fallback['model'], $config);
                $this->alertFallbackTriggered($provider, $fallback['provider'], $primaryException->getMessage(), fallbackSucceeded: true);

                return $result;
            } catch (Throwable $fallbackException) {
                $this->alertFallbackTriggered($provider, $fallback['provider'], $primaryException->getMessage(), fallbackSucceeded: false);

                throw new RuntimeException(
                    "LLM call failed on both [{$provider}] and fallback [{$fallback['provider']}]. ".
                    "Primary: {$primaryException->getMessage()} | Fallback: {$fallbackException->getMessage()}"
                );
            }
        }
    }

    /**
     * Lets admins know the shared, platform-wide provider key just failed —
     * worth surfacing even when the fallback quietly covers for it, since
     * every client's widget draws from that same key. Throttled per
     * provider pair so a real outage (many concurrent chats all failing
     * over at once) sends one alert, not one per message — and wrapped so a
     * notification failure (e.g. Zoho SMTP hiccup) can never break the
     * actual chat reply this step exists to produce.
     */
    private function alertFallbackTriggered(string $primaryProvider, string $fallbackProvider, string $primaryError, bool $fallbackSucceeded): void
    {
        $throttleKey = "llm-fallback-alert:{$primaryProvider}:{$fallbackProvider}:".($fallbackSucceeded ? 'recovered' : 'both-down');

        if (Cache::has($throttleKey)) {
            return;
        }

        Cache::put($throttleKey, true, now()->addMinutes(15));

        try {
            $admins = User::where('is_admin', true)->get();

            if ($admins->isNotEmpty()) {
                Notification::send($admins, new LlmProviderFallbackTriggered($primaryProvider, $fallbackProvider, $primaryError, $fallbackSucceeded));
            }
        } catch (Throwable $notificationException) {
            Log::warning('Failed to send LLM fallback alert', ['error' => $notificationException->getMessage()]);
        }
    }

    /**
     * @return array{provider: string, model: string}|null
     */
    private function resolveFallback(string $provider, array $config): ?array
    {
        if (filled($config['fallback_provider'] ?? null) && filled($config['fallback_model'] ?? null)) {
            return ['provider' => $config['fallback_provider'], 'model' => $config['fallback_model']];
        }

        return self::DEFAULT_FALLBACKS[$provider] ?? null;
    }

    private function call(string $provider, string $model, array $config): array
    {
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
                'model' => $model,
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
