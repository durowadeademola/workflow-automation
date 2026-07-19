<?php

namespace App\Services;

use App\Models\Subscription;
use App\Notifications\SubscriptionInvoice;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SubscriptionService
{
    /**
     * How much of a still-active subscription's value is unused, in Naira.
     * Free (₦0) subscriptions — including the trial — always credit ₦0,
     * since there's nothing paid to carry forward.
     */
    public function calculateProratedCredit(?Subscription $current): int
    {
        if (! $current || ! $current->isCurrentlyActive() || $current->amount <= 0) {
            return 0;
        }

        $totalDays = $current->start_date
            ? $current->start_date->copy()->startOfDay()->diffInDays($current->end_date->copy()->startOfDay())
            : 0;

        if ($totalDays <= 0) {
            $totalDays = 30;
        }

        // isCurrentlyActive() already guarantees end_date is in the future.
        $remainingDays = now()->startOfDay()->diffInDays($current->end_date->copy()->startOfDay());

        $fraction = min(1, $remainingDays / $totalDays);

        return (int) round($current->amount * $fraction);
    }

    /**
     * Activates a subscription — used both after a real Paystack payment
     * (with $paystackData from the verify/webhook response, for admin
     * auditing) and when a prorated credit fully covers the new plan's
     * price with no payment at all (no args). Idempotent: a no-op once
     * already active, so callback/webhook racing to activate the same
     * subscription never resets the billing period or double-invoices.
     *
     * @param  array<string, mixed>  $paystackData  Paystack's own "data" object
     */
    public function activateFree(Subscription $subscription, array $paystackData = []): void
    {
        if ($subscription->status === 'active') {
            return;
        }

        Subscription::where('client_id', $subscription->client_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->update(['status' => 'expired', 'is_active' => false]);

        $updates = [
            'status' => 'active',
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->billing_cycle === 'yearly' ? 365 : 30),
        ];

        if (! empty($paystackData)) {
            $updates['paystack_transaction_id'] = $paystackData['id'] ?? null;
            $updates['paystack_amount_charged'] = isset($paystackData['amount'])
                ? (int) round($paystackData['amount'] / 100)
                : null;
            $updates['paystack_channel'] = $paystackData['channel'] ?? null;
            $updates['paystack_paid_at'] = $paystackData['paid_at'] ?? null;
            $updates['paystack_gateway_response'] = $paystackData['gateway_response'] ?? null;
        }

        $subscription->update($updates);

        $this->sendInvoice($subscription);
    }

    private function sendInvoice(Subscription $subscription): void
    {
        $recipients = User::where('client_id', $subscription->client_id)
            ->where('is_client', true)
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send($recipients, new SubscriptionInvoice($subscription));
        } catch (\Throwable $e) {
            Log::warning('Failed to send subscription invoice', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
