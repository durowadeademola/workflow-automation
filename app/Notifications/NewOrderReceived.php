<?php

namespace App\Notifications;

use App\Models\Order;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class NewOrderReceived extends Notification
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
            ->title('New Order Received')
            ->success()
            ->body("Order #{$this->order->order_reference} is ready for processing.")
            ->actions([
                Action::make('view')->button()->url("/admin/orders/{$this->order->id}/edit"),
            ])
            ->getDatabaseMessage();
    }
}
