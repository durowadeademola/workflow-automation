<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * The actual email a journey's "send_email" step sends. Kept deliberately
 * plain (client-authored body_html rendered as-is, plus an unsubscribe
 * footer and open-tracking pixel) rather than Laravel's Notification
 * MailMessage builder, since that adds action-button/greeting styling not
 * suited to free-form marketing content.
 */
class MarketingJourneyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $bodyHtml,
        public string $businessName,
        public string $unsubscribeUrl,
        public string $openTrackingUrl,
    ) {}

    public function build(): self
    {
        return $this->subject($this->subjectLine)
            ->view('emails.marketing-journey')
            ->with([
                'bodyHtml' => $this->bodyHtml,
                'businessName' => $this->businessName,
                'unsubscribeUrl' => $this->unsubscribeUrl,
                'openTrackingUrl' => $this->openTrackingUrl,
            ]);
    }
}
