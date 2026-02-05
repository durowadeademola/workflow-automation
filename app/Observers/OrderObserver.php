<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Find the Client/Business owner
        $recipient = User::where('client_id', $order->client_id)
            ->where('is_client', true)
            ->first();

        if ($recipient) {
            Notification::make()
                ->title('New Order Received')
                ->success()
                ->body("Order #{$order->order_reference} is ready for processing.")
                ->actions([
                    Action::make('view')
                        ->button()
                        ->url(fn () => "/admin/orders/{$order->id}"),
                ])
                ->sendToDatabase($recipient);
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if the agent_id was just changed
        if ($order->isDirty('agent_id') && $order->agent_id) {

            // Find the User account linked to this Agent
            $agentUser = User::firstWhere('email', $order->agent->email);

            if ($agentUser) {
                Notification::make()
                    ->title('New Order')
                    ->info()
                    ->body("You have been assigned to Order #{$order->order_reference}.")
                    ->sendToDatabase($agentUser);
            }
        }
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        //
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        //
    }
}
