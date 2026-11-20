{{--
    Custom dashboard, built to the same structural pattern as gradebook-portal's React admin
    dashboard: a dark hero "band" (greeting, a one-line finding, action buttons, a status panel),
    a bordered KPI strip (not separate boxed cards), and a quiet two-column grid of bordered panels
    — no drop shadows at rest, 1px dividers only, tabular figures. Data comes from App\Filament\Pages\Dashboard::mount()
    into the $dashboard public property (role, firstName, and the current role's metrics).

    Only one of the three sections below renders — a user here holds exactly one of
    is_admin / is_client / is_agent (see Dashboard::mount()).
--}}
@php
    $role = $dashboard['role'] ?? null;
    $firstName = $dashboard['firstName'] ?? 'there';

    $hour = now()->hour;
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp

<x-filament-panels::page>
    <div class="flex flex-col gap-4">

        {{-- ── Band ─────────────────────────────────────────────────────────────────────── --}}
        <div class="rounded-[var(--bf-radius)] bg-[var(--bf-band-base)] text-[var(--bf-band-fg)]">
            <div class="flex flex-col gap-5 px-5 py-5 sm:px-6 sm:py-6 lg:flex-row lg:items-end lg:justify-between lg:gap-8">
                <div class="min-w-0">
                    <h1 class="bf-font-display m-0 text-[24px] font-medium leading-tight tracking-[-0.02em] sm:text-[28px]">
                        {{ $greeting }}, {{ $firstName }}
                    </h1>

                    @if ($role && !empty($dashboard['finding']))
                        <p class="m-0 mt-2 max-w-[60ch] text-[15px] text-[var(--bf-band-fg)]">{{ $dashboard['finding'] }}</p>
                    @elseif (!$role)
                        <p class="m-0 mt-2 max-w-[60ch] text-[15px] text-[var(--bf-band-fg-muted)]">Here's what's happening today.</p>
                    @endif

                    @if ($role && !empty($dashboard['actions']))
                        <div class="mt-4 flex flex-wrap gap-2">
                            @foreach ($dashboard['actions'] as $action)
                                <a
                                    href="{{ $action['url'] }}"
                                    class="inline-flex h-9 items-center gap-2 rounded-md px-3 text-[13px] font-semibold no-underline transition-colors
                                        {{ $action['primary']
                                            ? 'bg-[var(--bf-band-fg)] text-[var(--bf-band-base)] hover:bg-slate-200'
                                            : 'border border-[var(--bf-band-line)] text-[var(--bf-band-fg)] hover:bg-[var(--bf-band-glass)]' }}"
                                >
                                    {{ $action['label'] }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Status panel: the equivalent of gradebook's TermPanel. --}}
                @if ($role === 'admin')
                    <div class="w-full flex-none rounded-lg bg-[var(--bf-band-glass)] p-4 lg:w-[300px]">
                        <p class="m-0 text-[13px] text-[var(--bf-band-fg-muted)]">Monthly recurring revenue</p>
                        <p class="bf-font-display m-0 mt-1 text-2xl font-semibold tabular-nums tracking-[-0.01em]">
                            ₦{{ number_format($dashboard['mrr'] ?? 0) }}
                        </p>
                        <p class="m-0 mt-1 text-[13px] text-[var(--bf-band-fg-muted)]">From active subscriptions</p>
                    </div>
                @elseif ($role === 'client')
                    @php $sub = $dashboard['subscriptionSummary'] ?? ['status' => 'none', 'lines' => []]; @endphp
                    <div class="w-full flex-none rounded-lg bg-[var(--bf-band-glass)] p-4 lg:w-[340px]">
                        <div class="flex items-baseline justify-between gap-3">
                            <p class="m-0 text-[13px] text-[var(--bf-band-fg-muted)]">Subscription</p>
                            <p class="m-0 text-[13px] font-semibold">
                                {{ $sub['status'] === 'active' ? 'Active' : ($sub['status'] === 'expiring' ? 'Expiring' : 'None') }}
                            </p>
                        </div>
                        @foreach ($sub['lines'] as $line)
                            <p class="m-0 mt-1.5 text-[13px] leading-snug">{{ $line }}</p>
                        @endforeach
                    </div>
                @elseif ($role === 'agent')
                    <div class="w-full flex-none rounded-lg bg-[var(--bf-band-glass)] p-4 lg:w-[280px]">
                        <p class="m-0 text-[13px] text-[var(--bf-band-fg-muted)]">Resolved today</p>
                        <p class="bf-font-display m-0 mt-1 text-2xl font-semibold tabular-nums tracking-[-0.01em]">
                            {{ $dashboard['resolvedToday'] ?? 0 }}
                        </p>
                        <p class="m-0 mt-1 text-[13px] text-[var(--bf-band-fg-muted)]">
                            {{ $dashboard['messagesHandledToday'] ?? 0 }} agent {{ \Illuminate\Support\Str::plural('reply', $dashboard['messagesHandledToday'] ?? 0) }} sent today
                        </p>
                    </div>
                @endif
            </div>
        </div>

        @if ($role)
            {{-- ── KPI strip ────────────────────────────────────────────────────────────── --}}
            {{-- Tailwind's scanner reads this file's raw text, not the rendered HTML, so the grid-cols
                 utility for the KPI count has to appear as a complete literal string somewhere in this
                 file for either case — a `lg:grid-cols-{{ count(...) }}` interpolation would never be
                 picked up. divide-x/divide-y draws the 1px dividers between cells (including between
                 wrapped rows at small widths) without needing manual nth-child border bookkeeping. --}}
            <div class="grid grid-cols-2 divide-x divide-y divide-[var(--bf-border)] rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)] sm:grid-cols-3 {{ count($dashboard['kpis']) === 5 ? 'lg:grid-cols-5' : 'lg:grid-cols-6' }}">
                @foreach ($dashboard['kpis'] as $kpi)
                    <div class="px-4 py-4">
                        <p class="m-0 text-xs text-slate-500 dark:text-slate-400">{{ $kpi['label'] }}</p>
                        <p class="m-0 mt-1 text-xl font-semibold tabular-nums text-slate-900 dark:text-white sm:text-2xl">{{ $kpi['value'] }}</p>
                        <p class="m-0 mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $kpi['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            {{-- ── Two-column panel grid ───────────────────────────────────────────────── --}}
            <div class="grid grid-cols-1 items-start gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">

                @if ($role === 'admin')
                    <div class="flex min-w-0 flex-col gap-4">
                        {{-- Subscriptions --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Subscriptions</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Active and expiring soon</p>
                            </div>
                            <div class="px-5 py-4">
                                @php $split = $dashboard['subscriptionSplit']; $splitTotal = $split['active']; @endphp
                                @if ($splitTotal === 0)
                                    <p class="m-0 text-sm text-slate-500 dark:text-slate-400">No active subscriptions yet.</p>
                                @else
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-[28px] font-semibold leading-none tabular-nums text-slate-900 dark:text-white">{{ $splitTotal }}</span>
                                        <span class="text-[13px] text-slate-500 dark:text-slate-400">active, {{ $split['expiringSoon'] }} expiring within 7 days</span>
                                    </div>
                                    <div class="mt-3 flex h-3 gap-[2px] overflow-hidden rounded-full" role="img" aria-label="{{ $split['active'] - $split['expiringSoon'] }} steady, {{ $split['expiringSoon'] }} expiring soon">
                                        @if ($split['active'] - $split['expiringSoon'] > 0)
                                            <span class="h-full rounded-l-full" style="width: {{ round((($split['active'] - $split['expiringSoon']) / $splitTotal) * 100) }}%; background: rgb(16 185 129)"></span>
                                        @endif
                                        @if ($split['expiringSoon'] > 0)
                                            <span class="h-full rounded-r-full" style="width: {{ round(($split['expiringSoon'] / $splitTotal) * 100) }}%; background: rgb(245 158 11)"></span>
                                        @endif
                                    </div>
                                @endif

                                @if ($dashboard['expiringSubscriptions']->isNotEmpty())
                                    <table class="mt-4 w-full border-collapse text-[14px]">
                                        <thead>
                                            <tr class="text-left text-xs text-slate-500 dark:text-slate-400">
                                                <th class="pb-2 font-normal">Client</th>
                                                <th class="pb-2 font-normal">Plan</th>
                                                <th class="pb-2 text-right font-normal">Ends</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($dashboard['expiringSubscriptions'] as $sub)
                                                <tr class="border-t border-[var(--bf-border)]">
                                                    <td class="py-2.5 font-medium text-slate-900 dark:text-white">{{ $sub->client?->name ?? '—' }}</td>
                                                    <td class="py-2.5 text-slate-600 dark:text-slate-300">{{ $sub->name }}</td>
                                                    <td class="py-2.5 text-right tabular-nums text-slate-500 dark:text-slate-400">{{ $sub->end_date?->format('M j') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>

                        {{-- Clients --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Clients</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Most recently added</p>
                            </div>
                            <div class="px-5 py-2">
                                @forelse ($dashboard['recentClients'] as $client)
                                    <div class="flex items-center gap-3 border-t border-[var(--bf-border)] py-2.5 first:border-t-0">
                                        <div class="min-w-0 flex-1">
                                            <p class="m-0 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $client->name }}</p>
                                            <p class="m-0 truncate text-xs text-slate-500 dark:text-slate-400">{{ $client->email }}</p>
                                        </div>
                                        <span class="flex-none whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium"
                                            style="{{ $client->status === 'active'
                                                ? 'background: rgb(16 185 129 / 0.12); color: rgb(5 150 105)'
                                                : ($client->status === 'pending'
                                                    ? 'background: rgb(245 158 11 / 0.12); color: rgb(217 119 6)'
                                                    : 'background: rgb(100 116 139 / 0.12); color: rgb(71 85 105)') }}">
                                            {{ ucfirst($client->status) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="m-0 py-4 text-sm text-slate-500 dark:text-slate-400">No clients yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-col gap-4">
                        {{-- New leads --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">New leads</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Most recent</p>
                            </div>
                            <div class="px-5 py-2">
                                @forelse ($dashboard['recentLeads'] as $lead)
                                    <div class="border-t border-[var(--bf-border)] py-2.5 first:border-t-0">
                                        <p class="m-0 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $lead->name ?? $lead->business_name ?? 'Unnamed lead' }}</p>
                                        <p class="m-0 truncate text-xs text-slate-500 dark:text-slate-400">{{ $lead->source ?? 'Unknown source' }} · {{ $lead->created_at?->diffForHumans() }}</p>
                                    </div>
                                @empty
                                    <p class="m-0 py-4 text-sm text-slate-500 dark:text-slate-400">No leads yet.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                @elseif ($role === 'client')
                    <div class="flex min-w-0 flex-col gap-4">
                        {{-- Recent messages --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Recent messages</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Latest message per customer</p>
                            </div>
                            <div class="px-5 py-2">
                                @forelse ($dashboard['recentMessages'] as $message)
                                    <div class="flex items-start gap-3 border-t border-[var(--bf-border)] py-2.5 first:border-t-0">
                                        <div class="min-w-0 flex-1">
                                            <p class="m-0 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $message->customer?->display_name ?? 'Visitor' }}</p>
                                            <p class="m-0 line-clamp-1 text-xs text-slate-500 dark:text-slate-400">{{ $message->content }}</p>
                                        </div>
                                        <span class="flex-none whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium" style="background: rgb(31 111 168 / 0.1); color: rgb(31 111 168)">{{ $message->source }}</span>
                                        <span class="flex-none text-xs tabular-nums text-slate-500 dark:text-slate-400">{{ $message->created_at?->format('M j') }}</span>
                                    </div>
                                @empty
                                    <p class="m-0 py-4 text-sm text-slate-500 dark:text-slate-400">No messages yet.</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Appointments --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Upcoming appointments</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Not cancelled, from today</p>
                            </div>
                            <div class="px-5 py-2">
                                @forelse ($dashboard['upcomingAppointments'] as $appt)
                                    <div class="flex items-center gap-3 border-t border-[var(--bf-border)] py-2.5 first:border-t-0">
                                        <div class="min-w-0 flex-1">
                                            <p class="m-0 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $appt->name }}</p>
                                            <p class="m-0 truncate text-xs text-slate-500 dark:text-slate-400">{{ $appt->scheduled_at?->format('M j, g:i A') }}</p>
                                        </div>
                                        <span class="flex-none whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium"
                                            style="{{ $appt->status === 'confirmed'
                                                ? 'background: rgb(16 185 129 / 0.12); color: rgb(5 150 105)'
                                                : ($appt->status === 'pending'
                                                    ? 'background: rgb(245 158 11 / 0.12); color: rgb(217 119 6)'
                                                    : 'background: rgb(100 116 139 / 0.12); color: rgb(71 85 105)') }}">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="m-0 py-4 text-sm text-slate-500 dark:text-slate-400">No upcoming appointments.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-col gap-4">
                        {{-- Qualified leads gauge --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Qualified leads</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Of all customers</p>
                            </div>
                            <div class="px-5 py-4">
                                @if (($dashboard['totalCustomers'] ?? 0) === 0)
                                    <p class="m-0 text-sm text-slate-500 dark:text-slate-400">No customers yet.</p>
                                @else
                                    @php
                                        $size = 88; $stroke = 6; $r = ($size - $stroke) / 2; $c = 2 * M_PI * $r;
                                        $rate = $dashboard['qualifiedRate'];
                                    @endphp
                                    <div class="flex items-center gap-5">
                                        <div class="relative flex-none" role="img" aria-label="{{ $rate }}% qualified">
                                            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" aria-hidden="true" class="-rotate-90">
                                                <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke="var(--bf-border)" stroke-width="{{ $stroke }}" />
                                                <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke="#1f6fa8" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-dasharray="{{ $c * $rate / 100 }} {{ $c }}" />
                                            </svg>
                                            <span class="absolute inset-0 flex items-center justify-center text-lg font-semibold tabular-nums text-slate-900 dark:text-white">{{ $rate }}%</span>
                                        </div>
                                        <table class="w-full border-collapse text-[14px]">
                                            <tbody>
                                                <tr>
                                                    <td class="py-1 text-slate-500 dark:text-slate-400">Qualified</td>
                                                    <td class="py-1 text-right font-semibold tabular-nums text-slate-900 dark:text-white">{{ $dashboard['qualifiedLeads'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-1 text-slate-500 dark:text-slate-400">Total customers</td>
                                                    <td class="py-1 text-right font-semibold tabular-nums text-slate-900 dark:text-white">{{ $dashboard['totalCustomers'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                @elseif ($role === 'agent')
                    <div class="flex min-w-0 flex-col gap-4">
                        {{-- Conversations --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Conversations</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Waiting, active and resolved today</p>
                            </div>
                            <div class="px-5 py-4">
                                @php
                                    $cs = $dashboard['conversationSplit'];
                                    $csTotal = $cs['waiting'] + $cs['active'] + $cs['resolvedToday'];
                                @endphp
                                @if ($csTotal === 0)
                                    <p class="m-0 text-sm text-slate-500 dark:text-slate-400">No conversations yet today.</p>
                                @else
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-[28px] font-semibold leading-none tabular-nums text-slate-900 dark:text-white">{{ $csTotal }}</span>
                                        <span class="text-[13px] text-slate-500 dark:text-slate-400">conversations today</span>
                                    </div>
                                    <div class="mt-3 flex h-3 gap-[2px] overflow-hidden rounded-full" role="img" aria-label="{{ $cs['waiting'] }} waiting, {{ $cs['active'] }} active, {{ $cs['resolvedToday'] }} resolved">
                                        @foreach ([['v' => $cs['waiting'], 'c' => 'rgb(239 68 68)'], ['v' => $cs['active'], 'c' => 'rgb(31 111 168)'], ['v' => $cs['resolvedToday'], 'c' => 'rgb(16 185 129)']] as $seg)
                                            @if ($seg['v'] > 0)
                                                <span class="h-full first:rounded-l-full last:rounded-r-full" style="width: {{ round(($seg['v'] / $csTotal) * 100) }}%; background: {{ $seg['c'] }}"></span>
                                            @endif
                                        @endforeach
                                    </div>
                                    <table class="mt-3 w-full border-collapse text-[14px]">
                                        <tbody>
                                            <tr class="border-b border-[var(--bf-border)]">
                                                <td class="py-2"><span class="flex items-center gap-2 text-slate-700 dark:text-slate-200"><span class="h-2 w-2 rounded-full" style="background: rgb(239 68 68)"></span>Waiting for a reply</span></td>
                                                <td class="py-2 text-right font-semibold tabular-nums text-slate-900 dark:text-white">{{ $cs['waiting'] }}</td>
                                            </tr>
                                            <tr class="border-b border-[var(--bf-border)]">
                                                <td class="py-2"><span class="flex items-center gap-2 text-slate-700 dark:text-slate-200"><span class="h-2 w-2 rounded-full" style="background: rgb(31 111 168)"></span>Active</span></td>
                                                <td class="py-2 text-right font-semibold tabular-nums text-slate-900 dark:text-white">{{ $cs['active'] }}</td>
                                            </tr>
                                            <tr>
                                                <td class="py-2"><span class="flex items-center gap-2 text-slate-700 dark:text-slate-200"><span class="h-2 w-2 rounded-full" style="background: rgb(16 185 129)"></span>Resolved today</span></td>
                                                <td class="py-2 text-right font-semibold tabular-nums text-slate-900 dark:text-white">{{ $cs['resolvedToday'] }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                @endif

                                @if ($dashboard['waitingConversations']->isNotEmpty())
                                    <ul class="m-0 mt-4 list-none border-t border-[var(--bf-border)] p-0">
                                        @foreach ($dashboard['waitingConversations'] as $conv)
                                            <li class="flex items-center justify-between gap-3 border-b border-[var(--bf-border)] py-2.5 last:border-b-0">
                                                <span class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $conv->visitor_name ?: 'Visitor' }}</span>
                                                <span class="flex-none text-xs text-slate-500 dark:text-slate-400">waiting {{ $conv->waiting_since?->diffForHumans() }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>

                        {{-- Upcoming appointments --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Upcoming appointments</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Not cancelled, from today</p>
                            </div>
                            <div class="px-5 py-2">
                                @forelse ($dashboard['upcomingAppointments'] as $appt)
                                    <div class="flex items-center gap-3 border-t border-[var(--bf-border)] py-2.5 first:border-t-0">
                                        <div class="min-w-0 flex-1">
                                            <p class="m-0 truncate text-sm font-medium text-slate-900 dark:text-white">{{ $appt->name }}</p>
                                            <p class="m-0 truncate text-xs text-slate-500 dark:text-slate-400">{{ $appt->scheduled_at?->format('M j, g:i A') }} · {{ $appt->reason ?: 'No reason given' }}</p>
                                        </div>
                                        <span class="flex-none whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium"
                                            style="{{ $appt->status === 'confirmed'
                                                ? 'background: rgb(16 185 129 / 0.12); color: rgb(5 150 105)'
                                                : ($appt->status === 'pending'
                                                    ? 'background: rgb(245 158 11 / 0.12); color: rgb(217 119 6)'
                                                    : 'background: rgb(100 116 139 / 0.12); color: rgb(71 85 105)') }}">
                                            {{ ucfirst($appt->status) }}
                                        </span>
                                    </div>
                                @empty
                                    <p class="m-0 py-4 text-sm text-slate-500 dark:text-slate-400">No upcoming appointments.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="flex min-w-0 flex-col gap-4">
                        {{-- Qualified leads gauge --}}
                        <div class="rounded-[var(--bf-radius)] border border-[var(--bf-border)] bg-[var(--bf-surface)]">
                            <div class="border-b border-[var(--bf-border)] px-5 py-4">
                                <h3 class="bf-font-display m-0 text-[15px] font-semibold text-slate-900 dark:text-white">Qualified leads</h3>
                                <p class="m-0 text-xs text-slate-500 dark:text-slate-400">Of all customers</p>
                            </div>
                            <div class="px-5 py-4">
                                @if (($dashboard['totalCustomers'] ?? 0) === 0)
                                    <p class="m-0 text-sm text-slate-500 dark:text-slate-400">No customers yet.</p>
                                @else
                                    @php
                                        $size = 88; $stroke = 6; $r = ($size - $stroke) / 2; $c = 2 * M_PI * $r;
                                        $rate = $dashboard['qualifiedRate'];
                                    @endphp
                                    <div class="flex items-center gap-5">
                                        <div class="relative flex-none" role="img" aria-label="{{ $rate }}% qualified">
                                            <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" aria-hidden="true" class="-rotate-90">
                                                <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke="var(--bf-border)" stroke-width="{{ $stroke }}" />
                                                <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke="#1f6fa8" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-dasharray="{{ $c * $rate / 100 }} {{ $c }}" />
                                            </svg>
                                            <span class="absolute inset-0 flex items-center justify-center text-lg font-semibold tabular-nums text-slate-900 dark:text-white">{{ $rate }}%</span>
                                        </div>
                                        <table class="w-full border-collapse text-[14px]">
                                            <tbody>
                                                <tr>
                                                    <td class="py-1 text-slate-500 dark:text-slate-400">Qualified</td>
                                                    <td class="py-1 text-right font-semibold tabular-nums text-slate-900 dark:text-white">{{ $dashboard['qualifiedLeads'] }}</td>
                                                </tr>
                                                <tr>
                                                    <td class="py-1 text-slate-500 dark:text-slate-400">Total customers</td>
                                                    <td class="py-1 text-right font-semibold tabular-nums text-slate-900 dark:text-white">{{ $dashboard['totalCustomers'] }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif
    </div>
</x-filament-panels::page>
