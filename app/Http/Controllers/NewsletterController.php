<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscriber;
use Illuminate\View\View;

/**
 * Unauthenticated by necessity (email clients have no session) — keyed
 * purely by a NewsletterSend's own unguessable tracking_token. Branches on
 * subscriber_type since a Newsletter's audience is either Blueflow's own
 * NewsletterSubscriber rows or a client's subscribed_to_marketing Customers
 * (see NewsletterSender) — same reused unsubscribed.blade.php
 * MarketingTrackingController::unsubscribe() renders, since the confirmation
 * page's wording doesn't need to know which audience it was.
 */
class NewsletterController extends Controller
{
    public function unsubscribe(string $token): View
    {
        $send = NewsletterSend::where('tracking_token', $token)->first();

        $found = false;

        if ($send?->subscriber_type === 'customer') {
            $customer = Customer::find($send->subscriber_id);

            if ($customer && $customer->subscribed_to_marketing) {
                $customer->update([
                    'subscribed_to_marketing' => false,
                    'unsubscribed_at' => now(),
                ]);
            }

            $found = (bool) $customer;
        } elseif ($send?->subscriber_type === 'subscriber') {
            $subscriber = NewsletterSubscriber::find($send->subscriber_id);

            if ($subscriber && $subscriber->subscribed) {
                $subscriber->update([
                    'subscribed' => false,
                    'unsubscribed_at' => now(),
                ]);
            }

            $found = (bool) $subscriber;
        }

        return view('marketing.unsubscribed', ['found' => $found]);
    }
}
