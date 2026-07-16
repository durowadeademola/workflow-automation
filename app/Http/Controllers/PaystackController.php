<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Services\PaystackService;
use App\Services\SubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaystackController extends Controller
{
    public function __construct(
        private PaystackService $paystack,
        private SubscriptionService $subscriptions,
    ) {}

    /**
     * The browser lands here after the visitor finishes (or abandons) the
     * Paystack checkout. This gives immediate feedback; the webhook below
     * is the authoritative source of truth in case this redirect never
     * completes (tab closed, etc).
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference') ?? $request->query('trxref');
        $subscription = $reference ? Subscription::where('paystack_reference', $reference)->first() : null;

        if (! $subscription) {
            return redirect('/user/billing')->with('paystack_notice', [
                'type' => 'danger',
                'message' => 'We could not find that payment reference.',
            ]);
        }

        try {
            $result = $this->paystack->verifyTransaction($reference);
        } catch (\Throwable $e) {
            Log::warning('Paystack verify failed on callback', ['reference' => $reference, 'error' => $e->getMessage()]);

            return redirect('/user/billing')->with('paystack_notice', [
                'type' => 'warning',
                'message' => 'We could not confirm your payment yet. If you were charged, it will reflect shortly.',
            ]);
        }

        if (($result['data']['status'] ?? null) === 'success') {
            $this->activate($subscription, $result['data']);

            return redirect('/user/billing')->with('paystack_notice', [
                'type' => 'success',
                'message' => 'Payment confirmed — your subscription is now active.',
            ]);
        }

        $subscription->update([
            'status' => 'cancelled',
            'paystack_gateway_response' => $result['data']['gateway_response'] ?? null,
        ]);

        return redirect('/user/billing')->with('paystack_notice', [
            'type' => 'danger',
            'message' => 'Payment was not successful. You have not been charged.',
        ]);
    }

    /**
     * Server-to-server notification from Paystack. This is the source of
     * truth for activation — verified via HMAC signature, not trusted blindly.
     */
    public function webhook(Request $request)
    {
        $signature = $request->header('X-Paystack-Signature');
        $raw = $request->getContent();

        if (! $this->paystack->verifyWebhookSignature($raw, $signature)) {
            abort(401, 'Invalid signature.');
        }

        $event = $request->input('event');
        $reference = $request->input('data.reference');

        if ($event === 'charge.success' && $reference) {
            $subscription = Subscription::where('paystack_reference', $reference)->first();

            if ($subscription && $subscription->status !== 'active') {
                $this->activate($subscription, (array) $request->input('data', []));
            }
        }

        if ($event === 'charge.failed' && $reference) {
            $subscription = Subscription::where('paystack_reference', $reference)->first();

            // Only touch it if it's still pending — never overwrite a
            // subscription some other event (or the callback) already
            // resolved, e.g. a retry on the same reference that succeeded.
            if ($subscription && $subscription->status === 'pending') {
                $subscription->update([
                    'status' => 'cancelled',
                    'paystack_gateway_response' => $request->input('data.gateway_response'),
                ]);

                Log::info('Paystack charge failed', [
                    'reference' => $reference,
                    'subscription_id' => $subscription->id,
                    'reason' => $request->input('data.gateway_response'),
                ]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * The callback and webhook can both race to activate the same
     * subscription — activateFree() is a no-op past the first successful
     * call, so we never reset the billing period or send a duplicate
     * invoice regardless of which path gets there first.
     */
    private function activate(Subscription $subscription, array $paystackData = []): void
    {
        $this->subscriptions->activateFree($subscription, $paystackData);
    }
}
