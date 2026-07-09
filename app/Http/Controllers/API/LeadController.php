<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot: a real visitor never fills this hidden field in.
        if ($request->filled('website')) {
            return response()->json(['status' => 'success']);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'business_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'interest' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:255'],
        ]);

        $lead = Lead::create($validated);

        return response()->json([
            'status' => 'success',
            'data' => $lead,
        ], 201);
    }
}
