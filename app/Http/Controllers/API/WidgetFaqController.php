<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class WidgetFaqController extends Controller
{
    /**
     * FAQ answers are looked up straight from the database — unlike a chat
     * message, this never touches the AI or n8n, so it costs nothing and
     * never counts against a plan's message limit. Still gated behind the
     * same widget_enabled/subscription checks as chat, so nothing on a
     * disabled or unpaid widget works, not even the free stuff.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'clientId' => ['required', Rule::exists('clients', 'id')],
        ]);

        $client = Client::findOrFail($validated['clientId']);

        if (! $client->widget_enabled || ! $client->hasActiveSubscription()) {
            return response()->json(['faqs' => []]);
        }

        $faqs = $client->knowledgeBaseEntries()
            ->active()
            ->where('type', 'faq')
            ->orderBy('sort_order')
            ->get(['id', 'title', 'content']);

        return response()->json(['faqs' => $faqs]);
    }
}
