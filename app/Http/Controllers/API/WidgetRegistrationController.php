<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\CustomerRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class WidgetRegistrationController extends Controller
{
    /**
     * Called by n8n once the AI has collected a visitor's name plus phone
     * number — a lightweight "leave your details" path, distinct from
     * booking a specific appointment slot. Idempotent, same as lead
     * capture: re-firing for the same customer just updates the same row,
     * and only the first submission notifies the client.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'session_token' => ['required', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'interest' => ['nullable', 'string', 'max:500'],
        ]);

        // A graceful 200, not a validation error — n8n's "Capture
        // Registration" node has no error handling configured, so a 422
        // here would halt the workflow instead of letting the AI's own
        // reply (already written assuming success) through as-is.
        if (empty($validated['phone'])) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'A phone number is required to register — ask the visitor for one.',
            ], 200);
        }

        $client = Client::findOrFail($validated['client_id']);

        $customer = Customer::findOrCreateForChannel(
            $client->id,
            'Website',
            $validated['session_token'],
            $validated['name'],
        );

        $isFirstRegistration = ! $customer->registered_at;

        $customer->update([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? $customer->email,
            'phone' => $validated['phone'] ?? $customer->phone,
            'lead_intent' => $validated['interest'] ?? $customer->lead_intent,
            'registered_at' => $customer->registered_at ?? now(),
        ]);

        if ($isFirstRegistration) {
            $recipients = User::where('client_id', $client->id)
                ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                ->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new CustomerRegistered($customer));
            }
        }

        return response()->json(['status' => 'success'], 201);
    }
}
