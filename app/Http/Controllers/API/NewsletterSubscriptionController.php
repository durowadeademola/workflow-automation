<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;

/**
 * Public sign-up for Blueflow's own agency-level newsletter (the marketing
 * site's footer form) — not to be confused with a client's own Customers,
 * who opt in/out of Marketing Automation via subscribed_to_marketing
 * instead. Re-subscribing with the same email as a previously-unsubscribed
 * row reactivates it rather than erroring, since that's the least
 * surprising behavior for someone who changed their mind.
 */
class NewsletterSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        // Honeypot, same convention as LeadController: a real visitor never
        // fills this hidden field in.
        if ($request->filled('website')) {
            return response()->json(['status' => 'success']);
        }

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ]);

        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $validated['email']]);

        $subscriber->fill([
            'name' => $validated['name'] ?? $subscriber->name,
            'subscribed' => true,
            'subscribed_at' => $subscriber->subscribed_at ?? now(),
            'unsubscribed_at' => null,
        ])->save();

        return response()->json(['status' => 'success'], 201);
    }
}
