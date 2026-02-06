<div class="flex flex-col space-y-4 p-4 antialiased bg-slate-50 dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-inner"
style="max-height: 500px; overflow-y: auto;">
    @forelse($messages as $msg)
        @php
            // Logic to determine if message is from the customer or the business/agent
            $isCustomer = $msg->from_customer; 
        @endphp

        <div class="flex w-full {{ $isCustomer ? 'justify-start' : 'justify-end' }}">
            <div @class([
                'max-w-[85%] px-4 py-2.5 shadow-sm',
                'rounded-2xl rounded-tl-none bg-white text-gray-800 border border-gray-200' => $isCustomer,
                'rounded-2xl rounded-tr-none bg-primary-600 text-white' => !$isCustomer,
            ])>
                {{-- @if($isCustomer)
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-500 mb-1 block">
                        {{ $msg->customer?->username ?? 'Customer' }}
                    </span>
                @endif --}}

                <div @class([
                    'text-[9px] mt-1 flex justify-end items-center gap-1',
                    'text-gray-400' => $isCustomer,
                    'text-primary-100' => !$isCustomer,
                ])>
                    {{ $msg->created_at->format('d-m-y g:i A') }}: <span class="text-sm text-danger leading-relaxed whitespace-pre-wrap"> {{ $msg->content }}</span> 
                    @if(!$isCustomer)
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
            <div class="p-3 bg-gray-100 rounded-full mb-3">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
            </div>
            <p class="text-sm">No messages yet in this conversation.</p>
        </div>
    @endforelse
</div>