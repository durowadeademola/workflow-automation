<x-filament-panels::page>
    <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-100 dark:border-gray-800 p-6">
        <div class="flex items-center justify-between flex-wrap gap-4 mb-6">
            <div class="flex items-center gap-3">
                <a
                    href="{{ $this->getPreviousMonthUrl() }}"
                    class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100 w-40 text-center">
                    {{ $this->getCurrentMonth()->format('F Y') }}
                </h2>
                <a
                    href="{{ $this->getNextMonthUrl() }}"
                    class="p-2 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>

            <a
                href="{{ \App\Filament\Resources\Appointments\AppointmentResource::getUrl('index') }}"
                class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:text-primary-700"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                List view
            </a>
        </div>

        <div class="grid grid-cols-7 gap-px bg-gray-100 dark:bg-gray-800 rounded-lg overflow-hidden text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $label)
                <div class="bg-white dark:bg-[#111827] px-2 py-2 text-center">{{ $label }}</div>
            @endforeach
        </div>

        <div class="grid grid-cols-7 gap-px bg-gray-100 dark:bg-gray-800 border-x border-b border-gray-100 dark:border-gray-800 rounded-b-lg overflow-hidden">
            @foreach ($this->getCalendarDays() as $day)
                <div @class([
                    'bg-white dark:bg-[#111827] min-h-[100px] p-2 align-top',
                    'bg-gray-50/60 dark:bg-gray-800/40' => ! $day['inMonth'],
                ])>
                    <div @class([
                        'text-xs font-semibold mb-1.5',
                        'text-gray-800 dark:text-gray-200' => $day['inMonth'],
                        'text-gray-300 dark:text-gray-600' => ! $day['inMonth'],
                        'inline-flex items-center justify-center w-5 h-5 rounded-full bg-primary-600 text-white' => $day['date']->isToday(),
                    ])>
                        {{ $day['date']->day }}
                    </div>

                    <div class="space-y-1">
                        @foreach ($day['appointments'] as $appointment)
                            <a
                                href="{{ \App\Filament\Resources\Appointments\AppointmentResource::getUrl('edit', ['record' => $appointment]) }}"
                                @class([
                                    'block text-[11px] leading-snug rounded px-1.5 py-1 truncate transition-colors',
                                    'bg-amber-50 text-amber-800 hover:bg-amber-100 dark:bg-amber-950 dark:text-amber-300' => $appointment->status === 'pending',
                                    'bg-emerald-50 text-emerald-800 hover:bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300' => $appointment->status === 'confirmed',
                                    'bg-gray-100 text-gray-500 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-400' => in_array($appointment->status, ['completed', 'cancelled']),
                                ])
                                title="{{ $appointment->scheduled_at->format('g:i A') }} — {{ $appointment->name }}"
                            >
                                {{ $appointment->scheduled_at->format('g:i A') }} {{ $appointment->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center gap-4 mt-4 text-xs text-gray-500 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span> Pending</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Confirmed</span>
            <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-gray-400"></span> Completed / Cancelled</span>
        </div>
    </div>
</x-filament-panels::page>
