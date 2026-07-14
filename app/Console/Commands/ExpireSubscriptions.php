<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionExpired;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ExpireSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:expire-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Flip stale subscriptions to expired so the admin panel reflects reality (enforcement itself checks end_date directly and does not depend on this).';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $expiring = Subscription::where('status', 'active')
            ->where('end_date', '<', now()->startOfDay())
            ->get();

        foreach ($expiring as $subscription) {
            $subscription->update(['status' => 'expired', 'is_active' => false]);

            $recipients = User::where('client_id', $subscription->client_id)
                ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                ->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new SubscriptionExpired($subscription));
            }
        }

        $this->info("Expired {$expiring->count()} subscription(s).");
    }
}
