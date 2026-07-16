<x-filament-panels::page>
    @php
        $current = $this->getCurrentSubscription();
    @endphp

    {{-- Current status --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
        @if($current && $current->cancelled_at)
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Cancelled</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $current->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Your widget stays active until {{ $current->end_date?->format('M j, Y') }}
                        ({{ $current->end_date?->diffForHumans() }}), then won't renew. Subscribe to any plan below to resume.
                    </p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wide text-gray-600 bg-gray-100 rounded-full px-3 py-1">
                    Cancelled
                </span>
            </div>
        @elseif($current && $current->plan === 'trial')
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Free Trial</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">14-day trial</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Ends {{ $current->end_date?->format('M j, Y') }}
                        ({{ $current->end_date?->diffForHumans() }}) — subscribe below to keep your widget running after that.
                    </p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wide text-amber-700 bg-amber-100 rounded-full px-3 py-1">
                    Trial
                </span>
            </div>
        @elseif($current)
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Active Plan</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $current->name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">
                        Renews {{ $current->end_date?->format('M j, Y') }}
                        ({{ $current->end_date?->diffForHumans() }})
                    </p>
                </div>
                <div class="flex flex-col items-end gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700 bg-emerald-100 rounded-full px-3 py-1">
                        Active
                    </span>
                    <button
                        wire:click="mountAction('cancel')"
                        class="text-xs font-medium text-danger-600 hover:text-danger-700 transition-colors"
                    >
                        Cancel subscription
                    </button>
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <p class="text-sm text-gray-500">
                    @if($this->getRecentSubscriptions()->contains('plan', 'trial'))
                        Your free trial has ended and your chat widget is paused. Choose a plan below to turn it back on.
                    @else
                        You don't have an active subscription yet. Choose a plan below to get started.
                    @endif
                </p>
            </div>
        @endif

        @if($current)
            @php
                $usage = $this->getMessageUsage();
                $percent = $usage['limit'] ? min(100, (int) round($usage['used'] / $usage['limit'] * 100)) : 0;
            @endphp
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-medium text-gray-600 dark:text-gray-300">Messages this billing period</span>
                    <span class="text-gray-500">
                        {{ number_format($usage['used']) }}{{ $usage['limit'] ? ' / '.number_format($usage['limit']) : ' (unlimited)' }}
                    </span>
                </div>
                @if($usage['limit'])
                    <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div
                            class="h-full rounded-full {{ $percent >= 100 ? 'bg-danger-500' : ($percent >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                            style="width: {{ $percent }}%"
                        ></div>
                    </div>
                    @if($percent >= 100)
                        <p class="text-xs text-danger-600 mt-1.5">You've reached this period's message limit — the widget is paused until you upgrade or your plan renews.</p>
                    @elseif($percent >= 80)
                        <p class="text-xs text-amber-600 mt-1.5">Approaching your monthly message limit.</p>
                    @endif
                @endif
            </div>

            @php
                $apptUsage = $this->getAppointmentUsage();
                $apptPercent = $apptUsage['limit'] ? min(100, (int) round($apptUsage['used'] / $apptUsage['limit'] * 100)) : 0;
            @endphp
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-medium text-gray-600 dark:text-gray-300">Appointments this billing period</span>
                    <span class="text-gray-500">
                        {{ number_format($apptUsage['used']) }}{{ $apptUsage['limit'] ? ' / '.number_format($apptUsage['limit']) : ' (unlimited)' }}
                    </span>
                </div>
                @if($apptUsage['limit'])
                    <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div
                            class="h-full rounded-full {{ $apptPercent >= 100 ? 'bg-danger-500' : ($apptPercent >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                            style="width: {{ $apptPercent }}%"
                        ></div>
                    </div>
                    @if($apptPercent >= 100)
                        <p class="text-xs text-danger-600 mt-1.5">You've reached this period's appointment limit — the widget can't book new appointments until you upgrade or your plan renews.</p>
                    @elseif($apptPercent >= 80)
                        <p class="text-xs text-amber-600 mt-1.5">Approaching your monthly appointment limit.</p>
                    @endif
                @endif
            </div>

            @php
                $leadUsage = $this->getLeadUsage();
                $leadPercent = $leadUsage['limit'] ? min(100, (int) round($leadUsage['used'] / $leadUsage['limit'] * 100)) : 0;
            @endphp
            <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="font-medium text-gray-600 dark:text-gray-300">Qualified leads this billing period</span>
                    <span class="text-gray-500">
                        {{ number_format($leadUsage['used']) }}{{ $leadUsage['limit'] ? ' / '.number_format($leadUsage['limit']) : ' (unlimited)' }}
                    </span>
                </div>
                @if($leadUsage['limit'])
                    <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                        <div
                            class="h-full rounded-full {{ $leadPercent >= 100 ? 'bg-danger-500' : ($leadPercent >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                            style="width: {{ $leadPercent }}%"
                        ></div>
                    </div>
                    @if($leadPercent >= 100)
                        <p class="text-xs text-danger-600 mt-1.5">You've reached this period's lead qualification limit — new visitors won't be flagged as leads until you upgrade or your plan renews.</p>
                    @elseif($leadPercent >= 80)
                        <p class="text-xs text-amber-600 mt-1.5">Approaching your monthly lead qualification limit.</p>
                    @endif
                @endif
            </div>
        @endif
    </div>

    {{-- Plans --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        @foreach($this->getPlans() as $plan)
            <div @class([
                'rounded-2xl border p-6 bg-white dark:bg-gray-900 flex flex-col',
                'border-primary-500 ring-2 ring-primary-500' => $plan->is_popular,
                'border-gray-100 dark:border-gray-800' => ! $plan->is_popular,
            ])>
                @if($plan->is_popular)
                    <span class="text-[10px] font-semibold uppercase tracking-wide text-primary-700 bg-primary-100 rounded-full px-2 py-0.5 w-fit mb-3">
                        Most Popular
                    </span>
                @endif
                <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $plan->name }}</h3>
                @if($plan->description)
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $plan->description }}</p>
                @endif
                <p class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mt-2 mb-1">
                    ₦{{ number_format($plan->amount) }}<span class="text-sm font-normal text-gray-400">/month</span>
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                    {{ $plan->appointment_limit ? number_format($plan->appointment_limit).' appointments/month' : 'Unlimited appointments' }}
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    {{ $plan->lead_limit ? number_format($plan->lead_limit).' qualified leads/month' : 'Unlimited qualified leads' }}
                </p>

                @if($plan->features)
                    <ul class="space-y-2 mb-6 flex-1">
                        @foreach($plan->features as $feature)
                            <li class="flex items-start gap-2 text-xs text-gray-600 dark:text-gray-300">
                                <svg class="w-3.5 h-3.5 text-primary-500 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                                </svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($current && $current->plan === $plan->slug)
                    <button disabled class="mt-auto w-full text-center py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 text-sm font-semibold cursor-default">
                        Current Plan
                    </button>
                @else
                    <button
                        wire:click="mountAction('subscribe', { plan: '{{ $plan->slug }}' })"
                        wire:loading.attr="disabled"
                        class="mt-auto w-full text-center py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition-colors"
                    >
                        {{ $current ? 'Switch to ' . $plan->name : 'Subscribe' }}
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    {{-- History --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Billing History</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($this->getRecentSubscriptions() as $sub)
                <div class="px-4 py-3 flex items-center justify-between gap-4 text-sm">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $sub->name }}</p>
                        <p class="text-xs text-gray-400">{{ $sub->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-700 dark:text-gray-200">
                            ₦{{ number_format(max(0, ($sub->amount ?? 0) - ($sub->credit_applied ?? 0))) }}
                        </p>
                        @if($sub->credit_applied > 0)
                            <p class="text-[10px] text-emerald-600">₦{{ number_format($sub->credit_applied) }} credit applied</p>
                        @endif
                        <div class="flex items-center justify-end gap-2 mt-0.5">
                            <span @class([
                                'text-[10px] font-semibold uppercase tracking-wide rounded-full px-2 py-0.5',
                                'text-emerald-700 bg-emerald-100' => $sub->status === 'active' && ! $sub->cancelled_at,
                                'text-gray-600 bg-gray-100' => $sub->status === 'active' && $sub->cancelled_at,
                                'text-amber-700 bg-amber-100' => $sub->status === 'pending',
                                'text-gray-500 bg-gray-100' => in_array($sub->status, ['expired', 'cancelled']),
                            ])>
                                {{ $sub->status === 'active' && $sub->cancelled_at ? 'Cancelled' : ucfirst($sub->status) }}
                            </span>
                            @if($sub->start_date && $sub->plan !== 'trial')
                                <a
                                    href="{{ route('invoices.download', $sub) }}"
                                    target="_blank"
                                    class="text-[10px] font-semibold uppercase tracking-wide text-primary-600 hover:text-primary-700 hover:underline"
                                >
                                    Invoice
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center text-sm text-gray-400">
                    No billing history yet.
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
