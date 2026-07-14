<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Message;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Called by n8n for WhatsApp/Telegram flows to upsert a customer's
     * profile/conversation state, and (optionally) log the inbound message
     * that triggered this update — the same shape the website widget's
     * Customer/Message writes use, so all channels end up in one CRM view.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'platform' => ['required', 'string', 'max:255'],
            'chat_id' => ['required', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'from_customer' => ['nullable', 'boolean'],
            'product' => ['nullable', 'string', 'max:255'],
            'specs' => ['nullable', 'string'],
            'assigned_agent' => ['nullable', 'string', 'max:255'],
            'agent_email' => ['nullable', 'email', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
        ]);

        $customer = Customer::firstOrNew([
            'client_id' => $validated['client_id'],
            'platform' => $validated['platform'],
            'chat_id' => $validated['chat_id'],
        ]);

        $customer->fill(collect($validated)
            ->only(['name', 'username', 'state', 'product', 'specs', 'assigned_agent', 'agent_email', 'status'])
            ->filter(fn ($value) => $value !== null)
            ->all());

        if (! $customer->exists && blank($customer->status)) {
            $customer->status = 'OPEN';
        }

        // The `message` column is a "last message" snapshot for the customer
        // profile itself; the full history lives in the messages table.
        if (! empty($validated['message'])) {
            $customer->message = $validated['message'];
        }

        $customer->save();

        if (! empty($validated['message'])) {
            Message::create([
                'client_id' => $validated['client_id'],
                'customer_id' => $customer->id,
                'content' => $validated['message'],
                'source' => $validated['platform'],
                'from_customer' => $validated['from_customer'] ?? true,
            ]);
        }

        return response()->json([
            'status' => 'success',
            'data' => $customer,
        ], 201);
    }
}
