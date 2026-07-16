<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Customer;
use App\Models\Message;
use App\Services\MessageLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WidgetChatController extends Controller
{
    /**
     * The chat widget calls this instead of hitting n8n directly. This is
     * the single enforcement point for "stop working if unpaid" — the
     * widget never knows or supplies the n8n URL itself, so there's no way
     * for a client site to bypass the subscription check client-side.
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'clientId' => ['required', Rule::exists('clients', 'id')],
            'message' => ['required', 'string', 'max:4000'],
            'history' => ['array'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string'],
            'systemPrompt' => ['nullable', 'string'],
            'businessName' => ['nullable', 'string', 'max:255'],
            'waNumber' => ['nullable', 'string', 'max:50'],
            'sessionToken' => ['nullable', 'string', 'max:100'],
        ]);

        $client = Client::findOrFail($validated['clientId']);

        if (! $client->widget_enabled) {
            return response()->json([
                'reply' => "This assistant is currently turned off. Please reach out to us directly and we'll get back to you.",
                'blocked' => true,
            ], 403);
        }

        if (! $client->hasActiveSubscription()) {
            return response()->json([
                'reply' => "This assistant is temporarily unavailable. Please reach out to us directly and we'll get back to you.",
                'blocked' => true,
            ], 402);
        }

        if (! $client->webhook_url) {
            Log::warning('Client has an active subscription but no webhook_url configured', ['client_id' => $client->id]);

            return response()->json([
                'reply' => "This assistant isn't fully set up yet. Please reach out to us directly.",
                'blocked' => true,
            ], 502);
        }

        if ($client->hasReachedMessageLimit()) {
            app(MessageLimitService::class)->notifyOnceIfLimitReached($client);

            return response()->json([
                'reply' => "This assistant has reached its message limit for this month. Please reach out to us directly, or ask the business to upgrade their plan.",
                'blocked' => true,
            ], 429);
        }

        $customer = $this->logInboundMessage($client->id, $validated);

        try {
            $response = Http::timeout(20)->post($client->webhook_url, [
                'message' => $validated['message'],
                'history' => $validated['history'] ?? [],
                'systemPrompt' => $validated['systemPrompt'] ?? null,
                'businessName' => $validated['businessName'] ?? null,
                'clientId' => $client->id,
                'waNumber' => $validated['waNumber'] ?? null,
                'sessionToken' => $validated['sessionToken'] ?? null,
            ]);

            $data = $response->json() ?? [];

            $this->logOutboundReply($client->id, $customer, $data);

            return response()->json($data, $response->status());
        } catch (\Throwable $e) {
            Log::warning('Widget chat proxy failed to reach n8n', ['client_id' => $client->id, 'error' => $e->getMessage()]);

            return response()->json([
                'reply' => "Sorry, I'm having a little trouble right now. Please try again shortly.",
            ], 502);
        }
    }

    /**
     * Every widget conversation is identified by its session token, the
     * same way a Telegram/WhatsApp chat is identified by its chat id — so
     * the website shows up in Customers/Messages exactly like every other
     * channel. Logging failures here must never break the actual chat.
     */
    private function logInboundMessage(int $clientId, array $validated): ?Customer
    {
        if (empty($validated['sessionToken'])) {
            return null;
        }

        try {
            $customer = Customer::findOrCreateForChannel($clientId, 'Website', $validated['sessionToken']);

            Message::create([
                'client_id' => $clientId,
                'customer_id' => $customer->id,
                'content' => $validated['message'],
                'source' => 'Website',
                'from_customer' => true,
            ]);

            return $customer;
        } catch (\Throwable $e) {
            Log::warning('Failed to log widget chat message', ['client_id' => $clientId, 'error' => $e->getMessage()]);

            return null;
        }
    }

    private function logOutboundReply(int $clientId, ?Customer $customer, array $data): void
    {
        if (! $customer) {
            return;
        }

        $reply = $data['reply'] ?? $data['message'] ?? $data['output'] ?? null;

        if (! $reply) {
            return;
        }

        try {
            Message::create([
                'client_id' => $clientId,
                'customer_id' => $customer->id,
                'content' => $reply,
                'source' => 'Website',
                'from_customer' => false,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Failed to log widget chat reply', ['client_id' => $clientId, 'error' => $e->getMessage()]);
        }
    }
}
