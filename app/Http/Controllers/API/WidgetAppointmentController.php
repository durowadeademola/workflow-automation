<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Client;
use App\Models\Customer;
use App\Notifications\AppointmentBooked;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;

class WidgetAppointmentController extends Controller
{
    /**
     * Called by n8n once the AI has collected everything it needs to book
     * an appointment. Unlike most endpoints here, expected business outcomes
     * (slot taken, limit reached, no subscription) are returned as 200s with
     * a `status` field rather than HTTP error codes — n8n branches on that
     * field to have the AI relay a graceful message, instead of the request
     * just failing silently for the visitor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'session_token' => ['nullable', 'string', 'max:100'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'scheduled_at' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $client = Client::findOrFail($validated['client_id']);

        if (! $client->hasActiveSubscription()) {
            return response()->json([
                'status' => 'unavailable',
                'message' => 'This business does not currently have an active subscription, so appointments cannot be booked right now.',
            ]);
        }

        if ($client->hasReachedAppointmentLimit()) {
            return response()->json([
                'status' => 'limit_reached',
                'message' => "This business has reached its appointment booking limit for this billing period. Let the visitor know a team member will need to book this manually, and not to worry — they won't be charged twice.",
            ]);
        }

        $scheduledAt = \Illuminate\Support\Carbon::parse($validated['scheduled_at']);

        // Locks matching rows for the duration of the transaction so two
        // near-simultaneous requests for the same slot can't both pass the
        // conflict check before either has committed — the second request
        // waits, then correctly sees the first's row once it re-checks.
        $appointment = DB::transaction(function () use ($client, $validated, $scheduledAt) {
            $conflict = Appointment::where('client_id', $client->id)
                ->where('scheduled_at', $scheduledAt)
                ->where('status', '!=', 'cancelled')
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                return null;
            }

            $customer = null;

            if (filled($validated['session_token'] ?? null)) {
                $customer = Customer::findOrCreateForChannel(
                    $client->id,
                    'Website',
                    $validated['session_token'],
                    $validated['name'],
                );
            }

            return Appointment::create([
                'client_id' => $client->id,
                'customer_id' => $customer?->id,
                'name' => $validated['name'],
                'email' => $validated['email'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'scheduled_at' => $scheduledAt,
                'reason' => $validated['reason'] ?? null,
                'status' => 'pending',
                'source' => 'Website',
                'session_token' => $validated['session_token'] ?? null,
            ]);
        });

        if (! $appointment) {
            return response()->json([
                'status' => 'conflict',
                'message' => 'That date and time is already booked for someone else. Ask the visitor for a different time.',
            ]);
        }

        $recipients = User::where('client_id', $client->id)
            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new AppointmentBooked($appointment));
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $appointment->id,
                'scheduled_at' => $appointment->scheduled_at->toIso8601String(),
                'scheduled_at_human' => $appointment->scheduled_at->format('l, F j, Y \a\t g:i A'),
            ],
        ], 201);
    }
}
