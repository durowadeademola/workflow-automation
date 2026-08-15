<x-filament-panels::page>
    @php
        $activeSubscriptions = $this->getActiveSubscriptions();
    @endphp

    {{-- Your Active Plans — one card per service currently subscribed to, horizontal-scrolling since the count grows as more services get billing --}}
    @if(count($activeSubscriptions) > 0)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">Your Active Plans</h3>
            <div class="flex gap-4 overflow-x-auto pb-2 -mx-1 px-1">
                @foreach($activeSubscriptions as $service => $sub)
                    <div class="shrink-0 w-72 rounded-xl border border-gray-100 dark:border-gray-800 bg-white dark:bg-[#111827] p-5">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-[10px] font-semibold uppercase tracking-wide text-primary-700 bg-primary-100 rounded-full px-2 py-0.5">
                                {{ $this->getServiceLabel($service) }}
                            </span>
                            @if($sub->plan === 'trial')
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-amber-700 bg-amber-100 rounded-full px-2 py-0.5">Trial</span>
                            @elseif($sub->cancelled_at)
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-600 bg-gray-100 rounded-full px-2 py-0.5">Cancelled</span>
                            @elseif($sub->end_date && $sub->end_date->isPast())
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-amber-700 bg-amber-100 rounded-full px-2 py-0.5">Expiring</span>
                            @else
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700 bg-emerald-100 rounded-full px-2 py-0.5">Active</span>
                            @endif
                        </div>
                        <h4 class="text-base font-bold text-gray-900 dark:text-gray-100">{{ $sub->name }}</h4>
                        <p class="text-xs mt-1" @class(['text-amber-600 font-medium' => ! $sub->cancelled_at && $sub->end_date && $sub->end_date->isPast(), 'text-gray-500' => $sub->cancelled_at || ! $sub->end_date || ! $sub->end_date->isPast()])>
                            @if($sub->cancelled_at)
                                Access until {{ $sub->end_date?->format('M j, Y') }}
                            @elseif($sub->end_date && $sub->end_date->isPast())
                                Expired {{ $sub->end_date->diffForHumans() }} — resubscribe today to keep access
                            @else
                                Expires {{ $sub->end_date?->format('M j, Y') }} ({{ $sub->end_date?->diffForHumans() }})
                            @endif
                        </p>
                        @if(! $sub->cancelled_at)
                            <button
                                wire:click="mountAction('cancel', { service: '{{ $service }}' })"
                                class="text-xs font-medium text-danger-600 hover:text-danger-700 transition-colors mt-3"
                            >
                                Cancel subscription
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div x-data="{ cycle: 'monthly' }">
        {{-- Billing cycle toggle — shared across every service section below --}}
        <div class="flex items-center justify-center gap-3 mb-8">
            <span class="text-sm font-medium" :class="cycle === 'monthly' ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400'">Monthly</span>
            <button
                type="button"
                @click="cycle = cycle === 'monthly' ? 'yearly' : 'monthly'"
                class="relative w-11 h-6 rounded-full transition-colors"
                :class="cycle === 'yearly' ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-700'"
                aria-label="Toggle yearly billing"
            >
                <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform" :class="cycle === 'yearly' ? 'translate-x-5' : 'translate-x-0'"></span>
            </button>
            <span class="text-sm font-medium" :class="cycle === 'yearly' ? 'text-gray-900 dark:text-gray-100' : 'text-gray-400'">Yearly</span>
        </div>

        @foreach($this->getClientServices() as $service)
            @php
                $current = $this->getCurrentSubscription($service);
                $plans = $this->getPlans($service);
            @endphp

            <div class="mb-10">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-3">{{ $this->getServiceLabel($service) }}</h3>

                {{-- Current status + usage — only chat-widget has message/appointment/lead usage tracked today --}}
                <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-4">
                    @if($current && $current->cancelled_at)
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Cancelled</p>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $current->name }}</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Access stays on until {{ $current->end_date?->format('M j, Y') }}
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
                                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">14-day trial</h4>
                                <p class="text-sm text-gray-500 mt-1">
                                    Ends {{ $current->end_date?->format('M j, Y') }}
                                    ({{ $current->end_date?->diffForHumans() }}) — subscribe below to keep it running after that.
                                </p>
                            </div>
                            <span class="text-xs font-semibold uppercase tracking-wide text-amber-700 bg-amber-100 rounded-full px-3 py-1">
                                Trial
                            </span>
                        </div>
                    @elseif($current)
                        @php $isExpiring = $current->end_date && $current->end_date->isPast(); @endphp
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div>
                                <p @class(['text-xs font-semibold uppercase tracking-wide', 'text-amber-600' => $isExpiring, 'text-emerald-600' => ! $isExpiring])>{{ $isExpiring ? 'Expiring' : 'Active Plan' }}</p>
                                <h4 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $current->name }}</h4>
                                <p @class(['text-sm mt-1', 'text-amber-600 font-medium' => $isExpiring, 'text-gray-500' => ! $isExpiring])>
                                    @if($isExpiring)
                                        Expired {{ $current->end_date->diffForHumans() }} — resubscribe today to keep access
                                    @else
                                        Expires {{ $current->end_date?->format('M j, Y') }}
                                        ({{ $current->end_date?->diffForHumans() }})
                                    @endif
                                </p>
                            </div>
                            <span @class(['text-xs font-semibold uppercase tracking-wide rounded-full px-3 py-1', 'text-amber-700 bg-amber-100' => $isExpiring, 'text-emerald-700 bg-emerald-100' => ! $isExpiring])>
                                {{ $isExpiring ? 'Expiring' : 'Active' }}
                            </span>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">
                                You don't have an active {{ $this->getServiceLabel($service) }} subscription yet. Choose a plan below to get started.
                            </p>
                        </div>
                    @endif

                    @if($current && $service === 'chat-widget')
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
                    @elseif($current && $service === 'marketing-automation')
                        @php
                            $contactUsage = $this->getContactUsage();
                            $contactPercent = $contactUsage['limit'] ? min(100, (int) round($contactUsage['used'] / $contactUsage['limit'] * 100)) : 0;
                        @endphp
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-medium text-gray-600 dark:text-gray-300">Contacts</span>
                                <span class="text-gray-500">
                                    {{ number_format($contactUsage['used']) }}{{ $contactUsage['limit'] ? ' / '.number_format($contactUsage['limit']) : ' (unlimited)' }}
                                </span>
                            </div>
                            @if($contactUsage['limit'])
                                <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <div
                                        class="h-full rounded-full {{ $contactPercent >= 100 ? 'bg-danger-500' : ($contactPercent >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                                        style="width: {{ $contactPercent }}%"
                                    ></div>
                                </div>
                                @if($contactPercent >= 100)
                                    <p class="text-xs text-danger-600 mt-1.5">You've reached your plan's contact limit — adding or importing new contacts is blocked until you upgrade or remove some.</p>
                                @elseif($contactPercent >= 80)
                                    <p class="text-xs text-amber-600 mt-1.5">Approaching your contact limit.</p>
                                @endif
                            @endif
                        </div>

                        @php
                            $journeyUsage = $this->getJourneyUsage();
                            $journeyPercent = $journeyUsage['limit'] ? min(100, (int) round($journeyUsage['used'] / $journeyUsage['limit'] * 100)) : 0;
                        @endphp
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-medium text-gray-600 dark:text-gray-300">Active journeys</span>
                                <span class="text-gray-500">
                                    {{ number_format($journeyUsage['used']) }}{{ $journeyUsage['limit'] ? ' / '.number_format($journeyUsage['limit']) : ' (unlimited)' }}
                                </span>
                            </div>
                            @if($journeyUsage['limit'])
                                <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <div
                                        class="h-full rounded-full {{ $journeyPercent >= 100 ? 'bg-danger-500' : ($journeyPercent >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                                        style="width: {{ $journeyPercent }}%"
                                    ></div>
                                </div>
                                @if($journeyPercent >= 100)
                                    <p class="text-xs text-danger-600 mt-1.5">You've reached your plan's active journey limit — deactivate one or upgrade to create another.</p>
                                @elseif($journeyPercent >= 80)
                                    <p class="text-xs text-amber-600 mt-1.5">Approaching your active journey limit.</p>
                                @endif
                            @endif
                        </div>

                        @php
                            $emailUsage = $this->getEmailUsage();
                            $emailPercent = $emailUsage['limit'] ? min(100, (int) round($emailUsage['used'] / $emailUsage['limit'] * 100)) : 0;
                        @endphp
                        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between text-xs mb-1.5">
                                <span class="font-medium text-gray-600 dark:text-gray-300">Emails this billing period</span>
                                <span class="text-gray-500">
                                    {{ number_format($emailUsage['used']) }}{{ $emailUsage['limit'] ? ' / '.number_format($emailUsage['limit']) : ' (unlimited)' }}
                                </span>
                            </div>
                            @if($emailUsage['limit'])
                                <div class="w-full h-1.5 rounded-full bg-gray-100 dark:bg-gray-800 overflow-hidden">
                                    <div
                                        class="h-full rounded-full {{ $emailPercent >= 100 ? 'bg-danger-500' : ($emailPercent >= 80 ? 'bg-amber-500' : 'bg-primary-500') }}"
                                        style="width: {{ $emailPercent }}%"
                                    ></div>
                                </div>
                                @if($emailPercent >= 100)
                                    <p class="text-xs text-danger-600 mt-1.5">You've reached this period's email limit — sends are paused until you upgrade or your plan renews.</p>
                                @elseif($emailPercent >= 80)
                                    <p class="text-xs text-amber-600 mt-1.5">Approaching your monthly email limit.</p>
                                @endif
                            @endif
                        </div>
                    @endif
                </div>

                {{-- Plan cards for this service --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($plans as $plan)
                        @php
                            $isCurrentMonthly = $current && $current->plan === $plan->slug && $current->billing_cycle === 'monthly';
                            $isCurrentYearly = $current && $current->plan === $plan->slug && $current->billing_cycle === 'yearly';
                        @endphp
                        <div @class([
                            'rounded-2xl border p-6 bg-white dark:bg-[#111827] flex flex-col',
                            'border-primary-500 ring-2 ring-primary-500' => $plan->is_popular,
                            'border-gray-100 dark:border-gray-800' => ! $plan->is_popular,
                        ])>
                            <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-500 bg-gray-100 dark:bg-gray-800 dark:text-gray-400 rounded-full px-2 py-0.5 w-fit mb-3">
                                {{ $this->getServiceLabel($service) }}
                            </span>

                            <div class="flex items-center gap-2 flex-wrap mb-3">
                                @if($plan->is_popular)
                                    <span class="text-[10px] font-semibold uppercase tracking-wide text-primary-700 bg-primary-100 rounded-full px-2 py-0.5 w-fit">
                                        Most Popular
                                    </span>
                                @endif
                                @if($plan->has_active_promo)
                                    <span x-show="cycle === 'monthly'" class="text-[10px] font-semibold uppercase tracking-wide text-danger-700 bg-danger-100 rounded-full px-2 py-0.5 w-fit">
                                        {{ $plan->promo_percent }}% off
                                    </span>
                                @endif
                                @if($plan->has_yearly_discount)
                                    <span x-show="cycle === 'yearly'" x-cloak class="text-[10px] font-semibold uppercase tracking-wide text-danger-700 bg-danger-100 rounded-full px-2 py-0.5 w-fit">
                                        Save {{ $plan->yearly_discount_percent }}%
                                    </span>
                                @endif
                            </div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">{{ $plan->name }}</h3>
                            @if($plan->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $plan->description }}</p>
                            @endif

                            {{-- Monthly price --}}
                            <div x-show="cycle === 'monthly'">
                                @if($plan->has_active_promo)
                                    <p class="mt-2 mb-1">
                                        <span class="text-sm text-gray-400 line-through">₦{{ number_format($plan->amount) }}</span>
                                        <span class="text-2xl font-extrabold text-danger-600">₦{{ number_format($plan->promo_price) }}</span>
                                        <span class="text-sm font-normal text-gray-400">/month</span>
                                    </p>
                                    @if($plan->promo_ends_at)
                                        <p class="text-[11px] text-danger-600 mb-1">
                                            Offer ends {{ $plan->promo_ends_at->format('M j, Y') }} ({{ $plan->promo_ends_at->diffForHumans() }})
                                        </p>
                                    @endif
                                @else
                                    <p class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mt-2 mb-1">
                                        ₦{{ number_format($plan->amount) }}<span class="text-sm font-normal text-gray-400">/month</span>
                                    </p>
                                @endif
                            </div>

                            {{-- Yearly price --}}
                            <div x-show="cycle === 'yearly'" x-cloak>
                                @if($plan->has_yearly_discount)
                                    <p class="mt-2 mb-1">
                                        <span class="text-sm text-gray-400 line-through">₦{{ number_format($plan->yearly_regular_price) }}</span>
                                        <span class="text-2xl font-extrabold text-danger-600">₦{{ number_format($plan->yearly_effective_price) }}</span>
                                        <span class="text-sm font-normal text-gray-400">/year</span>
                                    </p>
                                @else
                                    <p class="text-2xl font-extrabold text-gray-900 dark:text-gray-100 mt-2 mb-1">
                                        ₦{{ number_format($plan->yearly_effective_price) }}<span class="text-sm font-normal text-gray-400">/year</span>
                                    </p>
                                @endif
                            </div>

                            @if($service === 'chat-widget')
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    {{ $plan->appointment_limit ? number_format($plan->appointment_limit).' appointments/month' : 'Unlimited appointments' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    {{ $plan->lead_limit ? number_format($plan->lead_limit).' qualified leads/month' : 'Unlimited qualified leads' }}
                                </p>
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    {{ $plan->contact_limit ? number_format($plan->contact_limit).' contacts' : 'Unlimited contacts' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    {{ $plan->journey_limit ? number_format($plan->journey_limit).' active journeys' : 'Unlimited active journeys' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                                    {{ $plan->email_send_limit ? number_format($plan->email_send_limit).' emails/month' : 'Unlimited emails/month' }}
                                </p>
                            @endif

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

                            @if($isCurrentMonthly)
                                <button x-show="cycle === 'monthly'" disabled class="mt-auto w-full text-center py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 text-sm font-semibold cursor-default">
                                    Current Plan
                                </button>
                            @else
                                <button
                                    x-show="cycle === 'monthly'"
                                    @click="$wire.mountAction('subscribe', { plan: '{{ $plan->slug }}', cycle: 'monthly' })"
                                    wire:loading.attr="disabled"
                                    class="mt-auto w-full text-center py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition-colors"
                                >
                                    {{ $current ? 'Switch to ' . $plan->name : 'Subscribe' }}
                                </button>
                            @endif

                            @if($isCurrentYearly)
                                <button x-show="cycle === 'yearly'" x-cloak disabled class="mt-auto w-full text-center py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-400 text-sm font-semibold cursor-default">
                                    Current Plan
                                </button>
                            @else
                                <button
                                    x-show="cycle === 'yearly'"
                                    x-cloak
                                    @click="$wire.mountAction('subscribe', { plan: '{{ $plan->slug }}', cycle: 'yearly' })"
                                    wire:loading.attr="disabled"
                                    class="mt-auto w-full text-center py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold transition-colors"
                                >
                                    {{ $current ? 'Switch to ' . $plan->name . ' (yearly)' : 'Subscribe yearly' }}
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- History --}}
    <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Billing History</h3>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($this->getRecentSubscriptions() as $sub)
                <div class="px-4 py-3 flex items-center justify-between gap-4 text-sm">
                    <div>
                        <div class="flex items-center gap-2">
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $sub->name }}</p>
                            <span class="text-[10px] font-semibold uppercase tracking-wide text-gray-500 bg-gray-100 dark:bg-gray-800 dark:text-gray-400 rounded-full px-2 py-0.5">
                                {{ $sub->serviceLabel() }}
                            </span>
                        </div>
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
                                <a
                                    href="{{ route('receipts.download', $sub) }}"
                                    target="_blank"
                                    class="text-[10px] font-semibold uppercase tracking-wide text-primary-600 hover:text-primary-700 hover:underline"
                                >
                                    Receipt
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
