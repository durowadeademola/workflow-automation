<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\ClientApproved;
use App\Notifications\ClientAwaitingApproval;
use App\Notifications\TrialStarted;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;

class ClientObserver
{
    /**
     * Handle the Client "created" event. Covers both onboarding paths: a
     * self-registered business starts "pending" and gets its trial later
     * (see updated()), while an admin creating one directly in Filament
     * defaults to "active" immediately — that business should start its
     * trial right away too, not miss out just because it skipped approval.
     */
    public function created(Client $client): void
    {
        if ($client->status === 'active') {
            $this->grantTrialIfEligible($client);

            return;
        }

        if ($client->status !== 'pending') {
            return;
        }

        $admins = User::where('is_admin', true)->get();

        if ($admins->isEmpty()) {
            return;
        }

        Notification::send($admins, new ClientAwaitingApproval($client));
    }

    /**
     * Handle the Client "updated" event. When a business flips to "active"
     * (approval, or reinstatement after being marked inactive), let its own
     * users know they can now log in — they have no other way to find out.
     *
     * Also covers a client that's already active gaining a new service
     * afterward (e.g. an admin ticks marketing-automation on in Filament) —
     * without this, grantTrialIfEligible() would only ever run at the
     * original approval, leaving a feature added later with no subscription
     * row at all (visible in the UI, but every limit reads as zero).
     */
    public function updated(Client $client): void
    {
        if ($client->status === 'active' && $client->wasChanged('features')) {
            $this->grantTrialIfEligible($client);
        }

        if (! $client->wasChanged('status')) {
            return;
        }

        if ($client->status !== 'active' || $client->getOriginal('status') === 'active') {
            return;
        }

        $this->grantTrialIfEligible($client);

        $recipients = User::where('client_id', $client->id)
            ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
            ->get();

        if ($recipients->isEmpty()) {
            return;
        }

        Notification::send($recipients, new ClientApproved());

        // Only reachable now that the client is active — see
        // UserObserver::created() and User::sendEmailVerificationNotification().
        foreach ($recipients as $recipient) {
            event(new Registered($recipient));
        }
    }

    /**
     * A business gets one free 14-day trial per service it selected, the
     * first time it's approved — never a second one for the same service,
     * even if it's later deactivated and reinstated. Each is just a
     * subscription row like any other, so the widget's existing
     * `hasActiveSubscription()` check and the daily expiry job both apply
     * to it with zero special-casing. Only services with a real trial
     * offering are listed here — the rest of Client::FEATURES exists for
     * the data model/admin tooling to be ready ahead of time, not to grant
     * trials for services that don't exist yet.
     */
    private function grantTrialIfEligible(Client $client): void
    {
        $recipients = null;

        foreach (Client::SELF_REGISTRATION_FEATURES as $service) {
            if (! $client->hasFeature($service)) {
                continue;
            }

            $alreadyHasOne = Subscription::where('client_id', $client->id)
                ->where(fn ($query) => $query->where('service', $service)
                    // Pre-migration rows (service = null) were always
                    // chat-widget, back when it was the only sellable
                    // service — never let chat-widget grant a second trial
                    // just because that legacy row has no `service` set.
                    ->when($service === 'chat-widget', fn ($q) => $q->orWhereNull('service')))
                ->exists();

            if ($alreadyHasOne) {
                continue;
            }

            $subscription = Subscription::create([
                'client_id' => $client->id,
                'service' => $service,
                'plan' => 'trial',
                'name' => 'Free Trial',
                'amount' => 0,
                'status' => 'active',
                'is_active' => true,
                'start_date' => now(),
                'end_date' => now()->addDays(14),
            ]);

            // Only ever reaches anyone here for the self-registered-then-
            // approved path, where the client's users already exist by this
            // point — for a client created "active" directly by an admin,
            // there's usually no user yet at all, so UserObserver::created()
            // sends this instead, the moment that first user actually shows
            // up. Queried once, lazily, since most clients only select one
            // service and this loop would otherwise re-query per service.
            $recipients ??= User::where('client_id', $client->id)
                ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                ->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new TrialStarted($subscription));
            }
        }
    }
}
