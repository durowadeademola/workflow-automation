<?php

namespace App\Workflow\Steps\Marketing;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * No SMS gateway account exists yet (see config('services.sms')). Same
 * graceful no-op shape as SendWhatsAppStep — visible as "skipped" in the
 * enrollment's send log rather than a silent fake-success or a hard
 * failure that would kill the whole journey for this customer.
 */
class SendSmsStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        if (! config('services.sms.api_key')) {
            return ['status' => 'skipped', 'reason' => 'channel_not_configured'];
        }

        // TODO: real SMS gateway send (e.g. Termii/Twilio), once
        // services.sms is actually configured. $context->get('trigger.customer.phone')
        // is the recipient; $config['body'] is the already merge-field-resolved text.
        return ['status' => 'skipped', 'reason' => 'channel_not_configured'];
    }
}
