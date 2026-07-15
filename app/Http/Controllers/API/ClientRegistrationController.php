<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            'features.*' => ['string', Rule::in(array_keys(Client::FEATURES))],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms_accepted' => ['accepted'],
        ]);

        $client = DB::transaction(function () use ($validated) {
            $client = Client::create([
                'name' => $validated['business_name'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'] ?? null,
                'type' => $validated['type'],
                'features' => $validated['features'],
                'status' => 'pending',
            ]);

            User::create([
                'client_id' => $client->id,
                'name' => $validated['business_name'],
                'email' => $validated['email'],
                'password' => $validated['password'],
                'is_admin' => false,
                'is_client' => true,
                'is_agent' => false,
            ]);

            return $client;
        });

        return response()->json([
            'status' => 'success',
            'data' => ['client_id' => $client->id],
        ], 201);
    }
}
