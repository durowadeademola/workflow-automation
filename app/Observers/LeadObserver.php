<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadConfirmation;
use App\Notifications\NewLeadReceived;
use Illuminate\Support\Facades\Notification;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        $admins = User::where('is_admin', true)->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new NewLeadReceived($lead));
        }

        $lead->notify(new LeadConfirmation());
    }
}
