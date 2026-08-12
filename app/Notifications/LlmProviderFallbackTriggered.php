<?php

namespace App\Notifications;

use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Tells admins the chat widget's primary LLM provider just failed and
 * LlmCallStep had to fall back to the other one — sent regardless of
 * whether the fallback itself then succeeded, since even a self-healed
 * failure is a signal worth knowing about (the primary provider's shared,
 * platform-wide key may be rate-limited or degraded, and every client's
 * chat widget is drawing from that same key).
 */
class LlmProviderFallbackTriggered extends Notification
{
    public function __construct(
        protected string $primaryProvider,
        protected string $fallbackProvider,
        protected string $primaryError,
        protected bool $fallbackSucceeded,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $outcome = $this->fallbackSucceeded
            ? "The fallback to {$this->fallbackProvider} succeeded, so visitors weren't affected this time."
            : "The fallback to {$this->fallbackProvider} also failed — chat replies are currently down until one of them recovers.";

        return (new MailMessage)
            ->subject($this->fallbackSucceeded
                ? "Chat widget: {$this->primaryProvider} had a hiccup (auto-recovered)"
                : "Chat widget: both LLM providers are failing — action needed")
            ->greeting("Hi {$notifiable->name},")
            ->line("The chat widget's primary LLM provider ({$this->primaryProvider}) just failed a request.")
            ->line("Error: {$this->primaryError}")
            ->line($outcome)
            ->line('This provider is a single shared key across every client\'s widget, so repeated failures are worth investigating even when the fallback covers for it.');
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $notification = FilamentNotification::make()
            ->title($this->fallbackSucceeded ? 'LLM fallback triggered (recovered)' : 'LLM fallback triggered — both providers failing')
            ->body("{$this->primaryProvider} failed: {$this->primaryError}");

        return ($this->fallbackSucceeded ? $notification->warning() : $notification->danger())
            ->getDatabaseMessage();
    }
}
