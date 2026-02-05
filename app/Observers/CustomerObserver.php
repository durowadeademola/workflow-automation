<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // Find the Client/Business owner
        $recipient = User::where('client_id', $customer->client_id)
            ->where('is_client', true)
            ->first();

        if ($recipient) {
            Notification::make()
                ->title('New Customer Alert')
                ->success()
                ->body("Customer #{$customer->chat_id} is ready for processing.")
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(fn () => "/admin/customers/{$customer->id}"),
                ])
                ->sendToDatabase($recipient);
        }
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        //
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        //
    }
}
