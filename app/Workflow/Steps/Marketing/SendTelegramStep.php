<?php

namespace App\Workflow\Steps\Marketing;

use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;

/**
 * No Telegram bot token exists yet (see config('services.telegram')). Same
 * graceful no-op shape as SendWhatsAppStep/SendSmsStep.
 */
class SendTelegramStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        if (! config('services.telegram.bot_token')) {
            return ['status' => 'skipped', 'reason' => 'channel_not_configured'];
        }

        // TODO: real Telegram Bot API send, once services.telegram is
        // actually configured. Would need the customer's Telegram chat ID
        // captured somewhere (not collected anywhere in this codebase yet).
        return ['status' => 'skipped', 'reason' => 'channel_not_configured'];
    }
}
