<?php

namespace App\Workflow\Steps\Marketing;

use App\Mail\MarketingJourneyMail;
use App\Workflow\Contracts\StepHandler;
use App\Workflow\WorkflowContext;
use Illuminate\Support\Facades\Mail;

/**
 * The only real (non-stubbed) channel today — sends through the already-
 * configured Zoho SMTP. $config['subject']/$config['body'] arrive with
 * merge-field placeholders (e.g. "{{trigger.customer.name}}") already
 * resolved by WorkflowContext, per the StepHandler contract.
 */
class SendEmailStep implements StepHandler
{
    public function execute(array $config, WorkflowContext $context): array
    {
        $email = $context->get('trigger.customer.email');

        if (empty($email)) {
            return ['status' => 'skipped', 'reason' => 'no_email'];
        }

        if ($context->get('trigger.customer.subscribedToMarketing') === false) {
            return ['status' => 'skipped', 'reason' => 'unsubscribed'];
        }

        $token = $context->get('trigger.send.trackingToken');

        Mail::to($email)->send(new MarketingJourneyMail(
            subjectLine: (string) ($config['subject'] ?? ''),
            bodyHtml: $this->rewriteLinksForClickTracking((string) ($config['body'] ?? ''), $token),
            businessName: (string) $context->get('trigger.client.name', 'us'),
            unsubscribeUrl: route('marketing.unsubscribe', ['token' => $token]),
            openTrackingUrl: route('marketing.track.open', ['token' => $token]),
        ));

        return ['status' => 'sent'];
    }

    /**
     * Client-authored body_html is free-form, so rather than requiring a
     * special merge field for the one link a client wants tracked, every
     * http(s) link in the content is wrapped in the click-redirect endpoint
     * — clicking any of them both records the click and still lands the
     * visitor on the original URL.
     */
    private function rewriteLinksForClickTracking(string $html, string $token): string
    {
        return preg_replace_callback(
            '/href="(https?:\/\/[^"]+)"/i',
            fn ($m) => 'href="'.route('marketing.track.click', ['token' => $token, 'url' => $m[1]]).'"',
            $html
        );
    }
}
