<?php

namespace App\Observers;

use App\Models\Client;
use App\Models\User;
use App\Notifications\TrialStarted;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Notification;

class UserObserver
{
    /**
     * Kicks off email verification the moment a client/agent user is first
     * able to log in. A pending client's users can't log in at all yet (see
     * User::canAccessPanel()), so firing this now would just mean the
     * verification link's short expiry window lapses before they can ever
     * use it — ClientObserver::updated() fires this instead, for every
     * existing user, at the moment their client actually goes active.
     */
    public function created(User $user): void
    {
        if (! ($user->is_client || $user->is_agent)) {
            return;
        }

        $client = $user->client;

        if ($client?->status !== 'active') {
            return;
        }

        event(new Registered($user));

        $this->notifyOfFreshTrial($user, $client);
    }

    /**
     * Covers a client created "active" directly by an admin: its trial
     * starts with no user around yet to tell (see
     * ClientObserver::grantTrialIfEligible()), so this catches up the moment
     * the first user actually appears — as long as it's still the SAME
     * trial that was just granted, not one an admin added long after, who'd
     * otherwise get a stale "your trial just started" email for a trial
     * that's already days old.
     */
    private function notifyOfFreshTrial(User $user, Client $client): void
    {
        $subscription = $client->currentSubscription();

        if (! $subscription || $subscription->plan !== 'trial') {
            return;
        }

        if (! $subscription->start_date || $subscription->start_date->diffInMinutes(now()) > 5) {
            return;
        }

        Notification::send($user, new TrialStarted($subscription));
    }
}
