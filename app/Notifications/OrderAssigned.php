<?php

namespace App\Notifications;

use App\Models\Order;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class OrderAssigned extends Notification
{
    public function __construct(protected Order $order) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('New Order')
            ->info()
            ->body("You have been assigned to Order #{$this->order->order_reference}.")
            ->getDatabaseMessage();
    }
}
