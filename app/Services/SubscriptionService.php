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
     * Activates a subscription outright with no payment step — used when a
     * prorated credit fully covers the new plan's price. Mirrors exactly
     * what PaystackController::activate() does after a real payment, minus
     * the payment itself, so both paths stay in sync (idempotent, one
     * active subscription per client, invoice sent).
     */
    public function activateFree(Subscription $subscription): void
    {
        if ($subscription->status === 'active') {
            return;
        }

        Subscription::where('client_id', $subscription->client_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->update(['status' => 'expired', 'is_active' => false]);

        $subscription->update([
            'status' => 'active',
            'is_active' => true,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
        ]);

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
