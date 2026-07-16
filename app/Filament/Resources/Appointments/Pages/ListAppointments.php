<?php

namespace App\Filament\Resources\Appointments\Pages;

use App\Filament\Pages\AppointmentCalendar;
use App\Filament\Resources\Appointments\AppointmentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('calendarView')
                ->label('Calendar view')
                ->icon(Heroicon::CalendarDays)
                ->color('gray')
                ->url(AppointmentCalendar::getUrl()),
            CreateAction::make()
                ->label('Log an appointment'),
        ];
    }
}
