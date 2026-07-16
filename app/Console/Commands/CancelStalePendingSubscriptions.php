<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

class CancelStalePendingSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:cancel-stale-pending-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancel checkout attempts abandoned over an hour ago — a client who never returns from Paystack (tab closed, no charge.failed webhook) would otherwise leave the subscription stuck in "pending" forever.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $stale = Subscription::where('status', 'pending')
            ->where('created_at', '<', now()->subHour())
            ->get();

        foreach ($stale as $subscription) {
            $subscription->update(['status' => 'cancelled']);
        }

        $this->info("Cancelled {$stale->count()} stale pending subscription(s).");
    }
}
