<?php

namespace App\Observers;

use App\Models\Appointment;
use App\Models\AutomationWorkflow;
use App\Workflow\JourneyEnrollment;

/**
 * Fires the appointment_booked Marketing Journey trigger. Unlike
 * abandoned_booking/re_engagement (both "absence of an event for N
 * hours/days", only detectable by an active scan — see
 * AdvanceMarketingJourneys), a booked appointment is a discrete event, so
 * enrollment happens immediately here rather than waiting for the next scan.
 */
class AppointmentObserver
{
    public function created(Appointment $appointment): void
    {
        if (! $appointment->customer_id) {
            return;
        }

        $workflows = AutomationWorkflow::where('client_id', $appointment->client_id)
            ->where('trigger_event', 'appointment_booked')
            ->where('is_active', true)
            ->get();

        if ($workflows->isEmpty()) {
            return;
        }

        $customer = $appointment->customer;

        if (! $customer) {
            return;
        }

        foreach ($workflows as $workflow) {
            JourneyEnrollment::enrollIfEligible($workflow, $customer);
        }
    }
}
