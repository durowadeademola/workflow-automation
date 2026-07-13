<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
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
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'message' => ['required', 'string', 'max:4000'],
            'history' => ['array'],
            'history.*.role' => ['required_with:history', 'in:user,assistant'],
            'history.*.content' => ['required_with:history', 'string'],
            'systemPrompt' => ['nullable', 'string'],
            'businessName' => ['nullable', 'string', 'max:255'],
            'waNumber' => ['nullable', 'string', 'max:50'],
            'sessionToken' => ['nullable', 'string', 'max:100'],
        ]);

        $client = Client::findOrFail($validated['client_id']);

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

            return response()->json($response->json() ?? [], $response->status());
        } catch (\Throwable $e) {
            Log::warning('Widget chat proxy failed to reach n8n', ['client_id' => $client->id, 'error' => $e->getMessage()]);

            return response()->json([
                'reply' => "Sorry, I'm having a little trouble right now. Please try again shortly.",
            ], 502);
        }
    }
}
