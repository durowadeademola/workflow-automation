<x-filament-panels::page>
    @php($customer = $this->record->customer)

    <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                    {{ $customer?->display_name ?? 'Unknown customer' }}
                </h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $customer?->username ? '@'.$customer->username.' · ' : '' }}{{ $this->record->source }}
                </p>
            </div>
        </div>

        <div class="flex flex-col space-y-1.5 p-4 bg-white dark:bg-[#111827] overflow-y-auto" style="max-height: 65vh;">
            @forelse($this->getThread() as $msg)
                <div class="flex w-full {{ $msg->from_customer ? 'justify-start' : 'justify-end' }}">
                    <div @class([
                        'max-w-[92%] px-2.5 py-1 rounded-xl shadow-sm text-sm leading-snug whitespace-pre-wrap',
                        'bg-white text-gray-800 border border-gray-200' => $msg->from_customer,
                        'bg-primary-600 text-white' => ! $msg->from_customer,
                    ])>
                        <span class="block text-[9px] font-semibold uppercase tracking-wide opacity-70">
                            {{ $msg->from_customer ? 'Customer' : 'Business' }}
                        </span>
                        {{ $msg->content }}
                        <span class="block text-[9px] opacity-60 mt-0.5">{{ $msg->created_at->format('M j, g:i A') }}</span>
                    </div>
                </div>
            @empty
                <div class="flex-1 flex items-center justify-center text-sm text-gray-400 py-10">
                    No messages found.
                </div>
            @endforelse
        </div>
    </div>
</x-filament-panels::page>
