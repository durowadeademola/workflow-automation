<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Laravel's default password-reset notification builds its link via
 * route('password.reset', ...), which only exists when you're using
 * Laravel's own auth scaffolding. This app's only login surface is the
 * Filament admin panel, whose reset-password route is namespaced under
 * the panel (filament.admin.auth.password-reset.reset) — so the default
 * notification 404s. This one takes an already-built, panel-aware URL.
 */
class ResetPasswordNotification extends Notification
{
    public function __construct(protected string $url) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Reset Password Notification')
            ->line('You are receiving this email because we received a password reset request for your account.')
            ->action('Reset Password', $this->url)
            ->line("This password reset link will expire in {$expireMinutes} minutes.")
            ->line('If you did not request a password reset, no further action is required.');
    }
}
