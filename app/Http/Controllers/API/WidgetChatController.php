<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Customer;
use App\Models\Message;
use App\Models\WidgetConversation;
use App\Services\AgentAssignmentService;
use App\Services\MessageLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class WidgetChatController extends Controller
{
    /**
     * Called once when the widget first loads, so a returning visitor (or
     * just a page reload) sees their prior conversation instead of a blank
     * slate — the widget itself keeps no state of its own beyond the
     * sessionToken, everything visible is reconstructed from here.
     *
     * `Message` is the unified log every channel mirrors into (see
     * WidgetConversationController::mirrorToMessages()), so this alone
     * covers both the AI phase and anything said during a human handoff.
     * `handoff` is additionally returned so the widget can resume polling
     * the right conversation instead of routing the visitor's next message
     * back through the AI while a human is actually already handling them.
     */
    public function history(Request $request)
    {
        $validated = $request->validate([
            'clientId' => ['required', Rule::exists('clients', 'id')],
            'sessionToken' => ['required', 'string', 'max:100'],
        ]);

        $customer = Customer::where('client_id', $validated['clientId'])
            ->where('platform', 'Website')
            ->where('chat_id', $validated['sessionToken'])
            ->first();

        $messages = $customer
            ? Message::where('customer_id', $customer->id)
                ->orderBy('created_at')
                ->get(['content', 'from_customer', 'sender_name', 'created_at'])
                ->map(fn (Message $message) => [
                    'role' => $message->from_customer ? 'user' : 'assistant',
                    'content' => $message->content,
                    // Only ever set for a human agent's own reply — left out
                    // for AI/system messages so the widget falls back to
                    // whatever the assistant is CURRENTLY named.
                    'senderName' => $message->sender_name,
                    'createdAt' => $message->created_at,
                ])
                ->all()
            : [];

        $conversation = WidgetConversation::where('client_id', $validated['clientId'])
            ->where('session_token', $validated['sessionToken'])
            ->whereIn('status', ['waiting', 'active'])
            ->first();

        return response()->json([
            'messages' => $messages,
            'handoff' => $conversation ? [
                'conversationId' => $conversation->id,
                'lastMessageId' => $conversation->messages()->max('id') ?? 0,
            ] : null,
        ]);
    }

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
            'sessionToken' => ['nullable', 'string', 'max:100'],
        ]);

        $client = Client::findOrFail($validated['clientId']);

        if (! $client->widget_enabled) {
            return response()->json([
                'reply' => "Our assistant is currently unavailable. Please reach out to us directly and we'll get back to you.",
                'blocked' => true,
            ], 403);
        }

        if (! $client->hasActiveSubscription()) {
            return response()->json([
                'reply' => "Our assistant is currently unavailable. Please reach out to us directly and we'll get back to you.",
                'blocked' => true,
            ], 402);
        }

        // The native engine doesn't need a webhook at all — it runs
        // in-process — so this is only a requirement for clients still on n8n.
        if (! $client->usesNativeWorkflowEngine() && ! $client->webhook_url) {
            Log::warning('Client has an active subscription but no webhook_url configured', ['client_id' => $client->id]);

            return response()->json([
                'reply' => "Our assistant is currently unavailable. Please reach out to us directly.",
                'blocked' => true,
            ], 502);
        }

        if ($client->hasReachedMessageLimit()) {
            app(MessageLimitService::class)->notifyOnceIfLimitReached($client);

            return response()->json([
                'reply' => "Our assistant is currently unavailable. Please reach out to us directly.",
                'blocked' => true,
            ], 429);
        }

        [$customer, $inboundMessage] = $this->logInboundMessage($client->id, $validated);

        $payload = [
            'message' => $validated['message'],
            'history' => $validated['history'] ?? [],
            // Authoritative from the client record actually looked up by
            // clientId, not whatever the widget happened to send — a
            // visitor tampering with their own browser's request could
            // otherwise override their own conversation's business name or
            // AI instructions. Same reasoning as knowledgeBase below,
            // which was already done this way.
            'systemPrompt' => $client->widget_system_prompt,
            'businessName' => $client->name,
            'clientId' => $client->id,
            'waNumber' => $client->widget_wa_number,
            'sessionToken' => $validated['sessionToken'] ?? null,
            'knowledgeBase' => $this->knowledgeBaseFor($client),
            // So the AI can correctly answer "are you open right now?"
            // itself, rather than the answer only ever surfacing indirectly
            // through the post-handoff override below. Null/false when
            // working_hours_enabled is off, matching the "always available"
            // default everywhere else.
            'workingHours' => $client->workingHoursDescription(),
            'isWithinWorkingHours' => $client->isWithinWorkingHours(),
        ];

        try {
            [$data, $statusCode] = $client->usesNativeWorkflowEngine()
                ? $this->runNativeEngine($payload)
                : $this->callN8n($client, $payload);

            // The definitive, self-contained guarantee that a visitor is
            // never actually connected to a human outside working hours, or
            // when this client simply has no active agent to receive the
            // handoff at all — independent of whatever the chat engine
            // itself decided to do. WidgetConversationController::store()
            // also refuses to create an agent ticket in either case, but
            // this is what the visitor actually sees, so it can't rely on
            // the engine cooperating.
            $noOneAvailable = ! $client->isWithinWorkingHours()
                || ! AgentAssignmentService::pickAgentFor($client->id);

            if (($data['handoff'] ?? false) && $noOneAvailable) {
                // Asks for name + a contact method rather than just saying
                // "no one's available" — the visitor's reply flows back
                // through the normal AI chat turn above, where
                // registerInstruction (see Build RAG Prompt) picks it up and
                // saves it via the existing registration flow, no separate
                // plumbing needed.
                $data['reply'] = $client->widget_wa_number
                    ? "Our team isn't available to chat right now. Could you share your name and phone number so an agent can reach you once available? You're also welcome to reach us directly on WhatsApp any time: https://wa.me/{$client->widget_wa_number}"
                    : "Our team isn't available to chat right now. Could you share your name and phone number so an agent can reach you once available?";
                $data['handoff'] = false;
                unset($data['conversationId'], $data['lastMessageId']);
            }

            $replied = $this->logOutboundReply($client->id, $customer, $data);

            if (! $replied) {
                $this->excludeFromLimit($inboundMessage);
            }

            return response()->json($data, $statusCode);
        } catch (\Throwable $e) {
            Log::warning('Widget chat engine failed to produce a reply', ['client_id' => $client->id, 'error' => $e->getMessage()]);

            $this->excludeFromLimit($inboundMessage);

            return response()->json([
                'reply' => "Sorry, I'm having currently unavailable. Please try again shortly.",
            ], 502);
        }
    }

    /**
     * @return array{0: array<string, mixed>, 1: int}
     */
    private function callN8n(Client $client, array $payload): array
    {
        $response = Http::timeout(20)->post($client->webhook_url, $payload);

        return [$response->json() ?? [], $response->status()];
    }

    /**
     * Runs the same pipeline through Blueflow's own AutomationWorkflow
     * engine (app/Workflow/) instead of n8n — per-client opt-in via
     * Client::workflow_engine (see usesNativeWorkflowEngine()), so clients can be
     * migrated one at a time while everyone else stays on n8n. See the
     * "chat-widget-reply" workflow seeded by migration for the exact steps
     * this runs.
     *
     * @return array{0: array<string, mixed>, 1: int}
     */
    private function runNativeEngine(array $payload): array
    {
        $workflow = \App\Models\AutomationWorkflow::where('slug', 'chat-widget-reply')->firstOrFail();
        $run = app(\App\Workflow\WorkflowExecutor::class)->run($workflow, $payload);

        if ($run->status !== 'completed') {
            throw new \RuntimeException($run->error ?? 'Native chat workflow run did not complete');
        }

        return [$run->context['steps']['respond'] ?? [], 200];
    }

    /**
     * n8n being unreachable, or replying with nothing usable, still gets a
     * friendly fallback message back to the visitor — but that's not a real
     * AI answer, so it shouldn't cost the client part of their plan's
     * message limit. Only the widget's own inbound log is ever adjusted
     * this way; every other channel's messages are untouched.
     */
    private function excludeFromLimit(?Message $message): void
    {
        $message?->update(['counts_toward_limit' => false]);
    }

    /**
     * Only "article" entries are sent here — "faq" entries are answered
     * directly from the database via WidgetFaqController/the widget's FAQ
     * tab instead, with no AI involved at all, so they're deliberately
     * excluded from what reaches the model.
     *
     * Looked up fresh on every message (not baked into the embed snippet
     * like systemPrompt is) so an edit takes effect immediately, and so the
     * content never has to round-trip through the visitor's browser. This
     * is handed to the AI alongside — not instead of — whatever RAG lookup
     * the n8n workflow itself performs.
     *
     * @return array<int, array{type: string, title: string, content: string}>
     */
    private function knowledgeBaseFor(Client $client): array
    {
        return $client->knowledgeBaseEntries()
            ->active()
            ->where('type', 'article')
            ->orderBy('sort_order')
            ->get(['type', 'title', 'content'])
            ->map(fn ($entry) => [
                'type' => $entry->type,
                'title' => $entry->title,
                'content' => $entry->content,
            ])
            ->all();
    }

    /**
     * Every widget conversation is identified by its session token, the
     * same way a Telegram/WhatsApp chat is identified by its chat id — so
     * the website shows up in Customers/Messages exactly like every other
     * channel. Logging failures here must never break the actual chat.
     *
     * Logged unconditionally (before we know if n8n will actually answer)
     * so the visitor's question is never lost from the conversation
     * history even on failure — it just gets excluded from the plan's
     * message count afterward if nothing came back, via excludeFromLimit().
     *
     * @return array{0: ?Customer, 1: ?Message}
     */
    private function logInboundMessage(int $clientId, array $validated): array
    {
        if (empty($validated['sessionToken'])) {
            return [null, null];
        }

        try {
            $customer = Customer::findOrCreateForChannel($clientId, 'Website', $validated['sessionToken']);

            $message = Message::create([
                'client_id' => $clientId,
                'customer_id' => $customer->id,
                'content' => $validated['message'],
                'source' => 'Website',
                'from_customer' => true,
            ]);

            return [$customer, $message];
        } catch (\Throwable $e) {
            Log::warning('Failed to log widget chat message', ['client_id' => $clientId, 'error' => $e->getMessage()]);

            return [null, null];
        }
    }

    private function logOutboundReply(int $clientId, ?Customer $customer, array $data): bool
    {
        if (! $customer) {
            return false;
        }

        $reply = $data['reply'] ?? $data['message'] ?? $data['output'] ?? null;

        if (! $reply) {
            return false;
        }

        try {
            Message::create([
                'client_id' => $clientId,
                'customer_id' => $customer->id,
                'content' => $reply,
                'source' => 'Website',
                'from_customer' => false,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::warning('Failed to log widget chat reply', ['client_id' => $clientId, 'error' => $e->getMessage()]);

            return false;
        }
    }
}
