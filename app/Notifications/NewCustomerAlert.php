<?php

namespace App\Notifications;

use App\Models\Customer;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Notifications\Notification;

class NewCustomerAlert extends Notification
{
    public function __construct(protected Customer $customer) {}

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
            ->title('New Customer Alert')
            ->success()
            ->body("Customer #{$this->customer->chat_id} is ready for processing.")
            ->actions([
                Action::make('view')->button()->url("/admin/customers/{$this->customer->id}/edit"),
            ])
            ->getDatabaseMessage();
    }
}
