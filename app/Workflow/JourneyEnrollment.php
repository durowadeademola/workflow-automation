<?php

namespace App\Workflow;

use App\Models\AutomationWorkflow;
use App\Models\AutomationWorkflowEnrollment;
use App\Models\Customer;

/**
 * Shared enrollment-creation logic used by every trigger path (the Filament
 * "Enroll a segment" action, the appointment_booked observer, and the
 * abandoned_booking/re_engagement scans in AdvanceMarketingJourneys) — so
 * "don't enroll the same customer in the same journey twice" is enforced
 * identically everywhere rather than re-implemented per call site.
 *
 * v1: one enrollment per (workflow, customer) ever, regardless of how that
 * prior enrollment ended — re-enrollment after exited/completed is an
 * explicit fast-follow, not needed for abandoned-booking/appointment/
 * re-engagement journeys, which each only make sense to run once per
 * customer.
 */
class JourneyEnrollment
{
    public static function enrollIfEligible(AutomationWorkflow $workflow, Customer $customer): ?AutomationWorkflowEnrollment
    {
        if (! $customer->subscribed_to_marketing) {
            return null;
        }

        $alreadyEnrolled = AutomationWorkflowEnrollment::where('automation_workflow_id', $workflow->id)
            ->where('customer_id', $customer->id)
            ->exists();

        if ($alreadyEnrolled) {
            return null;
        }

        return AutomationWorkflowEnrollment::create([
            'automation_workflow_id' => $workflow->id,
            'client_id' => $workflow->client_id,
            'customer_id' => $customer->id,
            'status' => 'active',
            'current_step_order' => 0,
            'next_run_at' => null, // due immediately, picked up by the next tick
            'enrolled_at' => now(),
        ]);
    }
}
