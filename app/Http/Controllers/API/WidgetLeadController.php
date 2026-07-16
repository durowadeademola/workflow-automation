<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\LeadQualified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class WidgetLeadController extends Controller
{
    /**
     * Called by n8n once the AI has naturally picked up on what a visitor
     * is interested in — no interrogation, no scoring, just "here's what
     * this conversation revealed." Idempotent: re-firing for the same
     * customer (the model isn't told to suppress repeats reliably) just
     * updates the same row rather than creating duplicates, and only the
     * first time notifies the client — otherwise a long conversation would
     * spam their inbox every time intent gets reiterated.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'session_token' => ['required', 'string', 'max:100'],
            'intent' => ['required', 'string', 'max:500'],
            'budget' => ['nullable', 'string', 'max:255'],
            'timeline' => ['nullable', 'string', 'max:255'],
        ]);

        $client = Client::findOrFail($validated['client_id']);

        $customer = Customer::findOrCreateForChannel(
            $client->id,
            'Website',
            $validated['session_token'],
        );

        $wasAlreadyQualified = $customer->is_qualified_lead;

        // Only a brand-new qualification counts against the plan's limit —
        // an already-qualified customer's intent/budget/timeline can still
        // be refined as the conversation continues.
        if (! $wasAlreadyQualified && $client->hasReachedLeadLimit()) {
            return response()->json(['status' => 'limit_reached'], 200);
        }

        $customer->update([
            'is_qualified_lead' => true,
            'lead_intent' => $validated['intent'],
            'lead_budget' => $validated['budget'] ?? $customer->lead_budget,
            'lead_timeline' => $validated['timeline'] ?? $customer->lead_timeline,
            'qualified_at' => $customer->qualified_at ?? now(),
        ]);

        if (! $wasAlreadyQualified) {
            $recipients = User::where('client_id', $client->id)
                ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                ->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new LeadQualified($customer));
            }
        }

        return response()->json(['status' => 'success'], 201);
    }
}
