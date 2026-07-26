<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:expire-subscriptions')->hourly();
Schedule::command('app:cancel-stale-pending-subscriptions')->hourly();
Schedule::command('app:remind-expiring-subscriptions')->hourly();

// Dispatches every active AutomationWorkflow with trigger_type=scheduled
// whose own cron expression (trigger_config.cron) is due — new scheduled
// workflows just need that config, not a new Schedule::command() entry here.
Schedule::command('workflows:run-scheduled')->everyMinute()->withoutOverlapping();
