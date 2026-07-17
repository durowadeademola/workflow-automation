<x-filament-panels::page>
    @php($ticket = $this->record)
    @php($isAdmin = auth()->user()->is_admin)

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden flex flex-col" style="min-height: 60vh;">
        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between gap-4 flex-wrap">
            <div>
                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ $ticket->subject }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    @if($isAdmin)
                        {{ $ticket->client?->name }} ·
                    @endif
                    Opened by {{ $ticket->user?->name ?? 'Unknown' }} · {{ $ticket->created_at->format('M j, Y') }}
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span @class([
                    'text-[10px] font-semibold uppercase tracking-wide rounded-full px-2.5 py-1',
                    'text-danger-700 bg-danger-100' => $ticket->status === 'open',
                    'text-success-700 bg-success-100' => $ticket->status === 'answered',
                    'text-gray-600 bg-gray-100' => $ticket->status === 'closed',
                ])>
                    {{ \App\Models\SupportTicket::STATUSES[$ticket->status] ?? $ticket->status }}
                </span>
                @if($ticket->status !== 'closed')
                    <button
                        wire:click="mountAction('closeTicket')"
                        class="text-xs font-semibold text-gray-500 hover:text-danger-600 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 transition-colors"
                    >
                        Close ticket
                    </button>
                @else
                    <button
                        wire:click="reopenTicket"
                        class="text-xs font-semibold text-gray-500 hover:text-primary-600 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 transition-colors"
                    >
                        Reopen
                    </button>
                @endif
            </div>
        </div>

        <div class="flex-1 flex flex-col space-y-3 p-4 bg-slate-50 dark:bg-gray-950 overflow-y-auto" style="max-height: 55vh;">
            @forelse($this->getThread() as $msg)
                <div class="flex w-full {{ $msg->from_admin ? 'justify-end' : 'justify-start' }}">
                    <div @class([
                        'max-w-[80%] px-4 py-2.5 rounded-2xl shadow-sm text-sm whitespace-pre-wrap',
                        'bg-white text-gray-800 border border-gray-200 rounded-tl-none' => ! $msg->from_admin,
                        'bg-primary-600 text-white rounded-tr-none' => $msg->from_admin,
                    ])>
                        <span class="block text-[10px] font-semibold uppercase tracking-wide opacity-70 mb-1">
                            {{ $msg->from_admin ? 'Support Team' : ($msg->user?->name ?? 'Client') }}
                        </span>
                        {{ $msg->content }}
                        <span class="block text-[10px] opacity-60 mt-1">{{ $msg->created_at->format('M j, g:i A') }}</span>
                    </div>
                </div>
            @empty
                <div class="flex-1 flex items-center justify-center text-sm text-gray-400 py-10">
                    No messages yet.
                </div>
            @endforelse
        </div>

        @if($ticket->status !== 'closed')
            <form wire:submit.prevent="sendReply" class="p-4 border-t border-gray-100 dark:border-gray-800">
                <textarea
                    wire:model="replyContent"
                    rows="3"
                    placeholder="Type your reply..."
                    class="w-full rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary-500"
                ></textarea>
                @error('replyContent')
                    <p class="text-xs text-danger-600 mt-1">{{ $message }}</p>
                @enderror
                <div class="flex justify-end mt-3">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        wire:target="sendReply"
                        class="bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl px-5 py-2.5 transition-colors"
                    >
                        Send Reply
                    </button>
                </div>
            </form>
        @else
            <div class="p-4 border-t border-gray-100 dark:border-gray-800 text-center text-xs text-gray-400">
                This ticket is closed. Reopen it to keep replying.
            </div>
        @endif
    </div>
</x-filament-panels::page>
