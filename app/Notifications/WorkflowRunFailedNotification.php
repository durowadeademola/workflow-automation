<?php

namespace App\Notifications;

use App\Models\AutomationWorkflowRun;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent to admins the moment a workflow run exhausts its retries and gives
 * up for good — the only proactive alert the native engine has; short of
 * this, a failed run is just a log line and a database row nobody's
 * watching. Always mail + database, unlike per-agent notifications — this
 * is an operational alert, not something a user preference should silence.
 */
class WorkflowRunFailedNotification extends Notification
{
    public function __construct(protected AutomationWorkflowRun $run) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $workflow = $this->run->workflow;

        return (new MailMessage)
            ->subject("Workflow \"{$workflow->name}\" run failed")
            ->greeting("Hi {$notifiable->name},")
            ->line("Run #{$this->run->id} of the \"{$workflow->name}\" workflow failed and gave up after exhausting its retries.")
            ->line($this->run->error ?? 'No error message was recorded.')
            ->action('Inspect this run', url('/workflow-studio'))
            ->line('Check `php artisan workflows:runs ' . $workflow->slug . " --run={$this->run->id}` for the full step-by-step detail.");
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $workflow = $this->run->workflow;

        return FilamentNotification::make()
            ->title("Workflow \"{$workflow->name}\" run failed")
            ->body($this->run->error ?? 'No error message was recorded.')
            ->danger()
            ->actions([
                Action::make('view')
                    ->button()
                    ->url('/workflow-studio')
                    ->label('Open Workflow Studio'),
            ])
            ->getDatabaseMessage();
    }
}
