<?php

namespace App\Console\Commands;

use App\Models\AutomationWorkflow;
use App\Models\AutomationWorkflowEnrollment;
use App\Models\Customer;
use App\Workflow\JourneyEnrollment;
use App\Workflow\JourneyStepAdvancer;
use Illuminate\Console\Command;

/**
 * The clock that drives Marketing Journeys — WorkflowExecutor has no notion
 * of pausing between steps, so journeys are advanced here instead, one due
 * step at a time per enrollment (see JourneyStepAdvancer), on whatever
 * schedule this command is registered on in routes/console.php.
 *
 * Also where the two scan-based behavior triggers live (abandoned_booking,
 * re_engagement) — unlike appointment_booked (a discrete event, handled by
 * an Appointment model observer instead), these are both "absence of an
 * event for N hours/days", which only an active periodic scan can detect.
 */
class AdvanceMarketingJourneys extends Command
{
    protected $signature = 'journeys:advance';

    protected $description = 'Auto-enrolls customers into abandoned-booking/re-engagement journeys, then advances every due journey enrollment by one step.';

    public function handle(JourneyStepAdvancer $advancer): int
    {
        $this->enrollAbandonedBookings();
        $this->enrollReEngagements();

        $enrollments = AutomationWorkflowEnrollment::where('status', 'active')
            ->where(fn ($q) => $q->whereNull('next_run_at')->orWhere('next_run_at', '<=', now()))
            ->get();

        foreach ($enrollments as $enrollment) {
            $advancer->advance($enrollment);
        }

        $this->info("Advanced {$enrollments->count()} due journey enrollment(s).");

        return self::SUCCESS;
    }

    /**
     * "Registered interest but never booked within N hours" — trigger_config
     * holds {"hours": N}. Only ever needs to fire once per customer, so a
     * customer JourneyEnrollment::enrollIfEligible() already turned away
     * (any prior enrollment, any status) is silently skipped, same as every
     * other trigger path.
     */
    private function enrollAbandonedBookings(): void
    {
        $workflows = AutomationWorkflow::where('trigger_event', 'abandoned_booking')
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            $hours = $workflow->trigger_config['hours'] ?? 24;

            $candidates = Customer::where('client_id', $workflow->client_id)
                ->whereNotNull('registered_at')
                ->where('registered_at', '<=', now()->subHours($hours))
                ->whereDoesntHave('appointments')
                ->get();

            foreach ($candidates as $customer) {
                JourneyEnrollment::enrollIfEligible($workflow, $customer);
            }
        }
    }

    /**
     * "No appointment or message activity in N days" — trigger_config holds
     * {"days": N}.
     */
    private function enrollReEngagements(): void
    {
        $workflows = AutomationWorkflow::where('trigger_event', 're_engagement')
            ->where('is_active', true)
            ->get();

        foreach ($workflows as $workflow) {
            $days = $workflow->trigger_config['days'] ?? 30;
            $cutoff = now()->subDays($days);

            $candidates = Customer::where('client_id', $workflow->client_id)
                ->whereDoesntHave('appointments', fn ($q) => $q->where('created_at', '>=', $cutoff))
                ->whereDoesntHave('messages', fn ($q) => $q->where('created_at', '>=', $cutoff))
                ->get();

            foreach ($candidates as $customer) {
                JourneyEnrollment::enrollIfEligible($workflow, $customer);
            }
        }
    }
}
