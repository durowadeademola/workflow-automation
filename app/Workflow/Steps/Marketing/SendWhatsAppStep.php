<?php

namespace App\Workflow\Steps\Marketing;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * No WhatsApp Business API account exists yet (see config('services.whatsapp')
 * — both keys are null until real credentials are added to .env). Rather
 * than fail the whole enrollment or silently pretend to send, this reports
 * itself as skipped so it's visible in the enrollment's send log. The
 * moment real credentials are configured, the actual provider call goes
 * where the TODO below is — no other code needs to change.
 */
class SendWhatsAppStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        if (! config('services.whatsapp.token')) {
            return ['status' => 'skipped', 'reason' => 'channel_not_configured'];
        }

        // TODO: real WhatsApp Business API send, once services.whatsapp is
        // actually configured. $context->get('trigger.customer.phone') is
        // the recipient; $config['body'] is the already merge-field-resolved
        // message text.
        return ['status' => 'skipped', 'reason' => 'channel_not_configured'];
    }
}
