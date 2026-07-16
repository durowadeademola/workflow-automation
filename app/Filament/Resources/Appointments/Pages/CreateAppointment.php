<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Exceptions\Halt;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['client_id'] = auth()->user()?->client_id;

        return $data;
    }

    /**
     * Manually logging a walk-in/phone booking should respect the exact
     * same plan limit as one booked through the AI — otherwise the limit
     * would only be a soft rule the client could route around themselves.
     */
    protected function beforeCreate(): void
    {
        if (auth()->user()?->client?->hasReachedAppointmentLimit()) {
            Notification::make()
                ->title('You\'ve reached your plan\'s appointment limit for this billing period.')
                ->danger()
                ->send();

            throw new Halt;
        }
    }
}
