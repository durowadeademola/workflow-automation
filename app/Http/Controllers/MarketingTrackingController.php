<?php

namespace App\Http\Controllers;

use App\Models\AutomationWorkflowEnrollmentSend;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Entirely unauthenticated — email clients rendering the open-tracking
 * pixel or following a link/unsubscribe URL have no session, no login.
 * Every action is keyed purely by the send's own unguessable tracking_token.
 */
class MarketingTrackingController extends Controller
{
    private const TRANSPARENT_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    public function open(string $token): Response
    {
        AutomationWorkflowEnrollmentSend::where('tracking_token', $token)
            ->whereNull('opened_at')
            ->update(['opened_at' => now()]);

        return response(base64_decode(self::TRANSPARENT_PNG_BASE64))
            ->header('Content-Type', 'image/png');
    }

    public function click(Request $request, string $token): RedirectResponse
    {
        AutomationWorkflowEnrollmentSend::where('tracking_token', $token)
            ->whereNull('clicked_at')
            ->update(['clicked_at' => now()]);

        // Falls back to the homepage if the URL is missing/malformed rather
        // than erroring — a broken tracked link should never dead-end a
        // real visitor.
        $url = $request->query('url');

        return redirect(filter_var($url, FILTER_VALIDATE_URL) ? $url : '/');
    }

    public function unsubscribe(string $token): \Illuminate\View\View
    {
        $send = AutomationWorkflowEnrollmentSend::where('tracking_token', $token)
            ->with('enrollment.customer')
            ->first();

        $customer = $send?->enrollment?->customer;

        if ($customer && $customer->subscribed_to_marketing) {
            $customer->update([
                'subscribed_to_marketing' => false,
                'unsubscribed_at' => now(),
            ]);
        }

        return view('marketing.unsubscribed', ['found' => (bool) $customer]);
    }
}
