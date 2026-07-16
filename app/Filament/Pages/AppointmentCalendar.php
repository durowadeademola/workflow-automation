<?php

namespace App\Filament\Pages;

use App\Models\Appointment;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;

/**
 * A lightweight custom calendar view for appointments — reached via a
 * "Calendar view" toggle button on the Appointments list (and vice versa),
 * not its own sidebar entry, since it's an alternate view of the same data
 * rather than a separate section.
 */
class AppointmentCalendar extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::CalendarDays;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.pages.appointment-calendar';

    public ?string $month = null;

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return (bool) $user && ($user->is_client || $user->is_agent) && $user->client?->hasFeature('chat-widget');
    }

    public function mount(): void
    {
        $this->month = request()->query('month') ?? now()->format('Y-m');
    }

    public function getCurrentMonth(): Carbon
    {
        return Carbon::createFromFormat('Y-m', $this->month)->startOfMonth();
    }

    public function getPreviousMonthUrl(): string
    {
        return static::getUrl(['month' => $this->getCurrentMonth()->copy()->subMonth()->format('Y-m')]);
    }

    public function getNextMonthUrl(): string
    {
        return static::getUrl(['month' => $this->getCurrentMonth()->copy()->addMonth()->format('Y-m')]);
    }

    /**
     * Every day of the calendar grid, including the leading/trailing days
     * from adjacent months needed to fill complete weeks (Monday start).
     *
     * @return array<int, array{date: Carbon, inMonth: bool, appointments: \Illuminate\Support\Collection}>
     */
    public function getCalendarDays(): array
    {
        $month = $this->getCurrentMonth();
        $gridStart = $month->copy()->startOfWeek(Carbon::MONDAY);
        $gridEnd = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $clientId = Auth::user()?->client_id;

        $appointments = Appointment::where('client_id', $clientId)
            ->whereBetween('scheduled_at', [$gridStart->copy()->startOfDay(), $gridEnd->copy()->endOfDay()])
            ->orderBy('scheduled_at')
            ->get()
            ->groupBy(fn (Appointment $appointment) => $appointment->scheduled_at->format('Y-m-d'));

        $days = [];
        $cursor = $gridStart->copy();

        while ($cursor->lte($gridEnd)) {
            $days[] = [
                'date' => $cursor->copy(),
                'inMonth' => $cursor->month === $month->month,
                'appointments' => $appointments->get($cursor->format('Y-m-d'), collect()),
            ];

            $cursor->addDay();
        }

        return $days;
    }
}
