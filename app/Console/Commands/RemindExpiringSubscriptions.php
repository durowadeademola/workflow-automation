<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
use App\Notifications\SubscriptionEndingSoon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class RemindExpiringSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:remind-expiring-subscriptions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Warn clients (trial or paid) a few days before their subscription ends, so it never lapses as a surprise.';

    /**
     * How many days before end_date the reminder goes out. Guarded by
     * expiry_reminder_sent_at so re-running this within the same window
     * (it's scheduled hourly, same as ExpireSubscriptions) never double-sends.
     */
    private const REMINDER_DAYS_BEFORE = 3;

    public function handle(): void
    {
        $subscriptions = Subscription::where('status', 'active')
            ->whereNull('expiry_reminder_sent_at')
            ->whereDate('end_date', now()->addDays(self::REMINDER_DAYS_BEFORE)->toDateString())
            ->get();

        foreach ($subscriptions as $subscription) {
            $recipients = User::where('client_id', $subscription->client_id)
                ->where(fn ($query) => $query->where('is_client', true)->orWhere('is_agent', true))
                ->get();

            if ($recipients->isNotEmpty()) {
                Notification::send($recipients, new SubscriptionEndingSoon($subscription));
            }

            $subscription->update(['expiry_reminder_sent_at' => now()]);
        }

        $this->info("Reminded {$subscriptions->count()} subscription(s) ending soon.");
    }
}
