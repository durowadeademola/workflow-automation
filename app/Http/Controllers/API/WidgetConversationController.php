<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\WidgetConversation;
use App\Models\WidgetMessage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WidgetConversationController extends Controller
{
    /**
     * Called by n8n when a visitor asks to speak with a human. Creates the
     * conversation (or reuses an existing open one for this visitor) and
     * seeds it with the transcript so far, so the agent has full context.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'session_token' => ['required', 'string', 'max:100'],
            'visitor_name' => ['nullable', 'string', 'max:255'],
            'transcript' => ['array'],
            'transcript.*.role' => ['required_with:transcript', 'in:user,assistant'],
            'transcript.*.content' => ['required_with:transcript', 'string'],
        ]);

        $client = Client::findOrFail($validated['client_id']);

        abort_unless($client->hasActiveSubscription(), 402, 'This client does not have an active subscription.');

        $conversation = WidgetConversation::where('client_id', $validated['client_id'])
            ->where('session_token', $validated['session_token'])
            ->where('status', '!=', 'closed')
            ->first();

        if (! $conversation) {
            $conversation = WidgetConversation::create([
                'client_id' => $validated['client_id'],
                'session_token' => $validated['session_token'],
                'visitor_name' => $validated['visitor_name'] ?? null,
                'status' => 'waiting',
            ]);

            foreach (($validated['transcript'] ?? []) as $entry) {
                WidgetMessage::create([
                    'widget_conversation_id' => $conversation->id,
                    'sender_type' => $entry['role'] === 'user' ? 'visitor' : 'ai',
                    'sender_name' => $entry['role'] === 'user' ? null : 'AI Assistant',
                    'content' => $entry['content'],
                ]);
            }

            $conversation->update(['last_message_at' => now()]);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'conversation_id' => $conversation->id,
                'session_token' => $conversation->session_token,
                'status' => $conversation->status,
                // Widget starts polling *after* this id, so the transcript
                // just seeded above isn't re-shown as if it were new.
                'last_message_id' => $conversation->messages()->max('id') ?? 0,
            ],
        ], 201);
    }

    /**
     * Polled by the widget while waiting for / talking to an agent.
     */
    public function messages(Request $request, WidgetConversation $conversation)
    {
        $this->authorizeToken($request, $conversation);
        $this->assertClientActive($conversation);

        $afterId = (int) $request->query('after_id', 0);

        $messages = $conversation->messages()
            ->where('id', '>', $afterId)
            ->orderBy('id')
            ->get(['id', 'sender_type', 'sender_name', 'content', 'created_at']);

        return response()->json([
            'status' => $conversation->status,
            'messages' => $messages,
        ]);
    }

    /**
     * Called by the widget to send a visitor message once handed off to a
     * human (bypasses the AI entirely from this point on).
     */
    public function send(Request $request, WidgetConversation $conversation)
    {
        $this->authorizeToken($request, $conversation);
        $this->assertClientActive($conversation);

        abort_if($conversation->status === 'closed', 409, 'This conversation has been closed.');

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:2000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_type' => 'visitor',
            'content' => $validated['content'],
        ]);

        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'status' => 'success',
            'data' => $message,
        ], 201);
    }

    private function authorizeToken(Request $request, WidgetConversation $conversation): void
    {
        $token = $request->query('token') ?? $request->input('token');

        abort_unless(
            $token && hash_equals($conversation->session_token, (string) $token),
            403,
            'Invalid conversation token.'
        );
    }

    private function assertClientActive(WidgetConversation $conversation): void
    {
        abort_unless(
            $conversation->client?->hasActiveSubscription(),
            402,
            'This client does not have an active subscription.'
        );
    }
}
