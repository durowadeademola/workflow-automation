<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use App\Notifications\ClientRegistered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ClientRegistrationController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: a real visitor never fills this hidden field in.
        if ($request->filled('website')) {
            return response()->json(['status' => 'success']);
        }

        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:clients,email', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'type' => ['required', 'string', 'max:255'],
            'features' => ['required', 'array', 'min:1'],
            'features.*' => ['string', Rule::in(Client::SELF_REGISTRATION_FEATURES)],
            'password' => ['required', 'confirmed', Password::min(8)->mixedCase()->numbers()->symbols()],
            'terms_accepted' => ['accepted'],
        ]);

        [$client, $user] = DB::transaction(function () use ($validated) {
            $client = Client::create([
                'name' => $validated['business_name'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'] ?? null,
                'type' => $validated['type'],
                'features' => $validated['features'],
                'status' => 'pending',
                'is_terms_accepted' => true,
                'terms_accepted_at' => now(),
            ]);

            $user = User::create([
                'client_id' => $client->id,
                'name' => $validated['business_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'is_admin' => false,
                'is_client' => true,
                'is_agent' => false,
            ]);

            return [$client, $user];
        });

        Notification::send($user, new ClientRegistered($client));

        return response()->json([
            'status' => 'success',
            'data' => ['client_id' => $client->id],
        ], 201);
    }
}
