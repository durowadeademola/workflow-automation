<?php

namespace App\Observers;

use App\Models\Lead;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class LeadObserver
{
    /**
     * Handle the Lead "created" event.
     */
    public function created(Lead $lead): void
    {
        $admins = User::where('is_admin', true)->get();

        foreach ($admins as $admin) {
            Notification::make()
                ->title('New Lead Received')
                ->success()
                ->body("{$lead->name} submitted the contact form.")
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(fn () => "/admin/leads/{$lead->id}/edit"),
                ])
                ->sendToDatabase($admin);
        }
    }
}
