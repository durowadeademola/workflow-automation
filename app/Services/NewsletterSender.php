<?php

namespace App\Services;

use App\Mail\NewsletterMail;
use App\Models\Customer;
use App\Models\Newsletter;
use App\Models\NewsletterSend;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

/**
 * Sends a Newsletter to its whole audience in one pass — client_id null
 * means Blueflow's own subscribers, client_id set means that client's own
 * subscribed_to_marketing Customers. Runs synchronously (same accepted
 * tradeoff as AdvanceMarketingJourneys/QUEUE_CONNECTION=sync elsewhere in
 * this app), so it's meant to be called from a Filament action, not a
 * request that needs to stay fast.
 */
class NewsletterSender
{
    /**
     * @return array{sent: int, eligible: int}
     */
    public function send(Newsletter $newsletter): array
    {
        if ($newsletter->status === 'sent') {
            return ['sent' => 0, 'eligible' => 0];
        }

        $newsletter->update(['status' => 'sending']);

        $client = $newsletter->client;
        $isClientOwned = $newsletter->client_id !== null;

        $recipients = $isClientOwned
            ? Customer::where('client_id', $newsletter->client_id)->where('subscribed_to_marketing', true)->get()
            : NewsletterSubscriber::where('subscribed', true)->get();

        $businessName = $client?->name ?? 'Blueflow Automation';
        $eligible = $recipients->count();
        $sent = 0;

        foreach ($recipients as $recipient) {
            if (empty($recipient->email)) {
                continue;
            }

            if ($client && $client->hasReachedEmailSendLimit()) {
                break;
            }

            $token = Str::random(40);

            $send = NewsletterSend::create([
                'newsletter_id' => $newsletter->id,
                'subscriber_type' => $isClientOwned ? 'customer' : 'subscriber',
                'subscriber_id' => $recipient->id,
                'tracking_token' => $token,
            ]);

            $name = $this->recipientName($recipient);

            Mail::to($recipient->email)->send(new NewsletterMail(
                subjectLine: str_replace('{{name}}', $name, $newsletter->subject),
                bodyHtml: str_replace('{{name}}', $name, $newsletter->body_html),
                businessName: $businessName,
                unsubscribeUrl: route('newsletter.unsubscribe', ['token' => $token]),
            ));

            $send->update(['sent_at' => now()]);
            $sent++;
        }

        $newsletter->update([
            'status' => 'sent',
            'sent_at' => now(),
            'recipients_count' => $sent,
        ]);

        return ['sent' => $sent, 'eligible' => $eligible];
    }

    private function recipientName(Customer|NewsletterSubscriber $recipient): string
    {
        return $recipient->name ?: 'there';
    }
}
