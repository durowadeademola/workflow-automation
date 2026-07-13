<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;

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
        $count = Subscription::where('status', 'active')
            ->where('end_date', '<', now()->startOfDay())
            ->update(['status' => 'expired', 'is_active' => false]);

        $this->info("Expired {$count} subscription(s).");
    }
}
