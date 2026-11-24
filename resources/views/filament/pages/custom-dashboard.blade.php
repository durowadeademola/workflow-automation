{{--
    Custom dashboard. The greeting, the one-line finding and the shortcut buttons are Filament's own
    page header (see App\Filament\Pages\Dashboard::getHeading()/getSubheading()/getHeaderActions()),
    so this view is just the KPI strip and the panel grid underneath. Data comes from
    Dashboard::mount() into the $dashboard public property. Styled by the .bf-kpi* and .bf-panel*
    rules in theme.css.

    Only one of the three sections below renders — a user here holds exactly one of
    is_admin / is_client / is_agent (see Dashboard::mount()).
--}}
@php
    $role = $dashboard['role'] ?? null;

    $statusColor = fn (?string $status): string => match ($status) {
        'active', 'confirmed' => 'success',
        'pending' => 'warning',
        'rejected', 'inactive' => 'danger',
        default => 'gray',
    };
@endphp

<x-filament-panels::page>
    @if ($role)
        <div class="flex flex-col gap-5">

            {{-- ── KPI strip ────────────────────────────────────────────────────────────────── --}}
            {{-- Tailwind's scanner reads this file's raw text, so both lg:grid-cols-* classes have to
                 appear as complete literals rather than an interpolated count. --}}
            <div class="bf-kpis grid grid-cols-2 sm:grid-cols-3 {{ count($dashboard['kpis']) === 5 ? 'lg:grid-cols-5' : 'lg:grid-cols-6' }}">
                @foreach ($dashboard['kpis'] as $kpi)
                    <div class="bf-kpi">
                        <p class="bf-kpi-label">{{ $kpi['label'] }}</p>
                        <p @class(['bf-kpi-value', 'bf-kpi-value-warning' => ($kpi['tone'] ?? null) === 'warning'])>{{ $kpi['value'] }}</p>
                        <p class="bf-kpi-desc">{{ $kpi['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- ── Panel grid ───────────────────────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(0,1fr)_380px]">

                @if ($role === 'admin')
                    <div class="flex min-w-0 flex-col gap-5">
                        <section class="bf-panel">
                            <header class="bf-panel-header">
                                <h3 class="bf-panel-title">Expiring subscriptions</h3>
                                <p class="bf-panel-desc">
                                    {{ $dashboard['subscriptionSplit']['active'] }} active, {{ $dashboard['subscriptionSplit']['expiringSoon'] }} ending within 7 days
                                </p>
                            </header>
                            @if ($dashboard['expiringSubscriptions']->isEmpty())
                                <p class="bf-panel-empty">Nothing ending in the next 7 days.</p>
                            @else
                                <table class="bf-panel-table">
                                    <thead>
                                        <tr>
                                            <th>Client</th>
                                            <th>Plan</th>
                                            <th class="text-right">Ends</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($dashboard['expiringSubscriptions'] as $sub)
                                            <tr>
                                                <td>
                                                    @include('filament.tables.columns.person', [
                                                        'name' => $sub->client?->name ?? '—',
                                                        'initials' => \App\Filament\Tables\Columns\PersonColumn::initials($sub->client?->name),
                                                        'subline' => $sub->client?->email,
                                                    ])
                                                </td>
                                                <td>{{ $sub->name }}</td>
                                                <td class="text-right tabular-nums">{{ $sub->end_date?->format('M j') }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </section>

                        <section class="bf-panel">
                            <header class="bf-panel-header">
                                <h3 class="bf-panel-title">Clients</h3>
                                <p class="bf-panel-desc">Most recently added</p>
                            </header>
                            @forelse ($dashboard['recentClients'] as $client)
                                <div class="bf-panel-row">
                                    @include('filament.tables.columns.person', [
                                        'name' => $client->name,
                                        'initials' => \App\Filament\Tables\Columns\PersonColumn::initials($client->name),
                                        'subline' => $client->email,
                                    ])
                                    <x-filament::badge :color="$statusColor($client->status)">{{ ucfirst($client->status) }}</x-filament::badge>
                                </div>
                            @empty
                                <p class="bf-panel-empty">No clients yet.</p>
                            @endforelse
                        </section>
                    </div>

                    <div class="flex min-w-0 flex-col gap-5">
                        <section class="bf-panel">
                            <header class="bf-panel-header">
                                <h3 class="bf-panel-title">New leads</h3>
                                <p class="bf-panel-desc">Most recent</p>
                            </header>
                            @forelse ($dashboard['recentLeads'] as $lead)
                                <div class="bf-panel-row">
                                    @include('filament.tables.columns.person', [
                                        'name' => $lead->name ?? $lead->business_name ?? 'Unnamed lead',
                                        'initials' => \App\Filament\Tables\Columns\PersonColumn::initials($lead->name ?? $lead->business_name),
                                        'subline' => ($lead->source ?? 'Unknown source').' · '.$lead->created_at?->diffForHumans(),
                                    ])
                                </div>
                            @empty
                                <p class="bf-panel-empty">No leads yet.</p>
                            @endforelse
                        </section>
                    </div>

                @elseif ($role === 'client')
                    <div class="flex min-w-0 flex-col gap-5">
                        <section class="bf-panel">
                            <header class="bf-panel-header">
                                <h3 class="bf-panel-title">Recent messages</h3>
                                <p class="bf-panel-desc">Latest message per customer</p>
                            </header>
                            @forelse ($dashboard['recentMessages'] as $message)
                                <div class="bf-panel-row">
                                    @include('filament.tables.columns.person', [
                                        'name' => $message->customer?->display_name ?? 'Visitor',
                                        'initials' => \App\Filament\Tables\Columns\PersonColumn::initials($message->customer?->display_name ?? 'Visitor'),
                                        'subline' => \Illuminate\Support\Str::limit($message->content, 80),
                                    ])
                                    <div class="flex flex-none items-center gap-3">
                                        <x-filament::badge color="gray">{{ $message->source }}</x-filament::badge>
                                        <span class="bf-panel-meta">{{ $message->created_at?->format('M j') }}</span>
                                    </div>
                                </div>
                            @empty
                                <p class="bf-panel-empty">No messages yet.</p>
                            @endforelse
                        </section>

                        <section class="bf-panel">
                            <header class="bf-panel-header">
                                <h3 class="bf-panel-title">Upcoming appointments</h3>
                                <p class="bf-panel-desc">Not cancelled, from today</p>
                            </header>
                            @forelse ($dashboard['upcomingAppointments'] as $appt)
                                <div class="bf-panel-row">
                                    @include('filament.tables.columns.person', [
                                        'name' => $appt->name,
                                        'initials' => \App\Filament\Tables\Columns\PersonColumn::initials($appt->name),
                                        'subline' => $appt->scheduled_at?->format('M j, g:i A'),
                                    ])
                                    <x-filament::badge :color="$statusColor($appt->status)">{{ ucfirst($appt->status) }}</x-filament::badge>
                                </div>
                            @empty
                                <p class="bf-panel-empty">No upcoming appointments.</p>
                            @endforelse
                        </section>
                    </div>

                    <div class="flex min-w-0 flex-col gap-5">
                        @php $sub = $dashboard['subscriptionSummary'] ?? ['status' => 'none', 'lines' => []]; @endphp
                        <section class="bf-panel">
                            <header class="bf-panel-header flex items-start justify-between gap-3">
                                <div>
                                    <h3 class="bf-panel-title">Subscription</h3>
                                    <p class="bf-panel-desc">Your current plans</p>
                                </div>
                                <x-filament::badge :color="match ($sub['status']) { 'active' => 'success', 'expiring' => 'warning', default => 'gray' }">
                                    {{ $sub['status'] === 'active' ? 'Active' : ($sub['status'] === 'expiring' ? 'Expiring' : 'None') }}
                                </x-filament::badge>
                            </header>
                            @foreach ($sub['lines'] as $line)
                                <div class="bf-panel-row bf-panel-text">{{ $line }}</div>
                            @endforeach
                        </section>

                        @include('filament.pages.partials.dashboard-qualified-leads')
                    </div>

                @elseif ($role === 'agent')
                    <div class="flex min-w-0 flex-col gap-5">
                        <section class="bf-panel">
                            <header class="bf-panel-header">
                                <h3 class="bf-panel-title">Waiting for a reply</h3>
                                <p class="bf-panel-desc">
                                    {{ $dashboard['conversationSplit']['waiting'] }} waiting, {{ $dashboard['conversationSplit']['active'] }} active, {{ $dashboard['conversationSplit']['resolvedToday'] }} resolved today
                                </p>
                            </header>
                            @forelse ($dashboard['waitingConversations'] as $conv)
                                <div class="bf-panel-row">
                                    @include('filament.tables.columns.person', [
                                        'name' => $conv->visitor_name ?: 'Visitor',
                                        'initials' => \App\Filament\Tables\Columns\PersonColumn::initials($conv->visitor_name ?: 'Visitor'),
                                        'subline' => 'Waiting '.$conv->waiting_since?->diffForHumans(),
                                    ])
                                    <a href="/user/live-chat" class="bf-panel-link">Reply</a>
                                </div>
                            @empty
                                <p class="bf-panel-empty">Nothing waiting for you right now.</p>
                            @endforelse
                        </section>

                        <section class="bf-panel">
                            <header class="bf-panel-header">
                                <h3 class="bf-panel-title">Upcoming appointments</h3>
                                <p class="bf-panel-desc">Not cancelled, from today</p>
                            </header>
                            @forelse ($dashboard['upcomingAppointments'] as $appt)
                                <div class="bf-panel-row">
                                    @include('filament.tables.columns.person', [
                                        'name' => $appt->name,
                                        'initials' => \App\Filament\Tables\Columns\PersonColumn::initials($appt->name),
                                        'subline' => $appt->scheduled_at?->format('M j, g:i A').' · '.($appt->reason ?: 'No reason given'),
                                    ])
                                    <x-filament::badge :color="$statusColor($appt->status)">{{ ucfirst($appt->status) }}</x-filament::badge>
                                </div>
                            @empty
                                <p class="bf-panel-empty">No upcoming appointments.</p>
                            @endforelse
                        </section>
                    </div>

                    <div class="flex min-w-0 flex-col gap-5">
                        @include('filament.pages.partials.dashboard-qualified-leads')
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
