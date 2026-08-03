<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * A one-off Newsletter broadcast's actual email. Same plain-Mailable
 * approach as MarketingJourneyMail (client-authored body_html as-is, plus an
 * unsubscribe footer) — no open-tracking pixel, since Newsletters don't
 * carry the per-step analytics a journey enrollment does, just the
 * unsubscribe link this feature actually asked for.
 */
class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $subjectLine,
        public string $bodyHtml,
        public string $businessName,
        public string $unsubscribeUrl,
    ) {}

    public function build(): self
    {
        return $this->subject($this->subjectLine)
            ->view('emails.newsletter')
            ->with([
                'bodyHtml' => $this->bodyHtml,
                'businessName' => $this->businessName,
                'unsubscribeUrl' => $this->unsubscribeUrl,
            ]);
    }
}
