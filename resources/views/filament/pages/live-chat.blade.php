<x-filament-panels::page>
    <div wire:poll.5s="$refresh" class="grid grid-cols-1 lg:grid-cols-3 gap-6" style="min-height: 60vh;">
        {{-- Conversation list --}}
        <div class="lg:col-span-1 bg-[var(--bf-surface)] rounded-[var(--bf-radius)] border border-[var(--bf-border)] overflow-hidden">
            <div class="px-4 py-3 border-b border-[var(--bf-border)]">
                <h3 class="bf-font-display text-sm font-semibold text-gray-700 dark:text-gray-200">Conversations</h3>
            </div>
            <div class="divide-y divide-[var(--bf-border)]" style="max-height: 65vh; overflow-y: auto;">
                @forelse($this->getConversations() as $conversation)
                    <button
                        wire:click="selectConversation({{ $conversation->id }})"
                        @class([
                            'w-full text-left px-4 py-3 transition-colors',
                            'bg-[rgb(31_111_168_/_0.08)] dark:bg-[rgb(128_192_233_/_0.12)]' => $selectedConversationId === $conversation->id,
                            'hover:bg-gray-50 dark:hover:bg-gray-800' => $selectedConversationId !== $conversation->id,
                        ])
                    >
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-100 truncate">
                                {{ $conversation->visitor_name ?: 'Website Visitor #' . $conversation->id }}
                            </span>
                            @if($conversation->status === 'waiting')
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-amber-700 bg-amber-100 rounded-full px-2 py-0.5 flex-shrink-0">
                                    Waiting
                                </span>
                            @else
                                <span class="text-[10px] font-semibold uppercase tracking-wide text-emerald-700 bg-emerald-100 rounded-full px-2 py-0.5 flex-shrink-0">
                                    Active
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ $conversation->last_message_at?->diffForHumans() ?? $conversation->created_at->diffForHumans() }}
                        </p>
                        <p class="text-[11px] mt-1">
                            @if($conversation->agent_id === auth()->id())
                                <span class="text-[#1f6fa8] dark:text-[#80c0e9] font-semibold">Assigned to you</span>
                            @elseif($conversation->agent)
                                <span class="text-gray-400">Assigned to {{ $conversation->agent->name }}</span>
                            @else
                                <span class="text-gray-400">Unassigned</span>
                            @endif
                        </p>
                    </button>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-gray-400">
                        No open conversations right now.
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Thread --}}
        <div class="lg:col-span-2 bg-[var(--bf-surface)] rounded-[var(--bf-radius)] border border-[var(--bf-border)] flex flex-col overflow-hidden">
            @php($conversation = $this->getSelectedConversation())

            @if(! $conversation)
                <div class="flex-1 flex items-center justify-center text-sm text-gray-400 py-20">
                    Select a conversation to view and reply.
                </div>
            @else
                <div class="px-4 py-3 border-b border-[var(--bf-border)] flex items-center justify-between">
                    <div>
                        <h3 class="bf-font-display text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $conversation->visitor_name ?: 'Website Visitor #' . $conversation->id }}
                        </h3>
                        <p class="text-xs text-gray-400">{{ $conversation->client?->name }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if($conversation->status !== 'closed')
                            <button
                                wire:click="mountAction('returnToAI')"
                                class="text-xs font-semibold text-gray-500 hover:text-[#1f6fa8] dark:hover:text-[#80c0e9] border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 transition-colors"
                            >
                                Return to AI
                            </button>
                        @endif
                        <button
                            wire:click="mountAction('closeConversation')"
                            class="text-xs font-semibold text-gray-500 hover:text-danger-600 border border-gray-200 dark:border-gray-700 rounded-lg px-3 py-1.5 transition-colors"
                        >
                            Close conversation
                        </button>
                    </div>
                </div>

                <div class="flex-1 flex flex-col space-y-1.5 p-2 bg-[var(--bf-surface)] overflow-y-auto" style="max-height: 38vh;">
                    @forelse($conversation->messages()->orderBy('id')->get() as $msg)
                        <div class="flex w-full {{ $msg->sender_type === 'visitor' ? 'justify-start' : 'justify-end' }}">
                            <div @class([
                                'max-w-[60%] px-2 py-1 rounded-lg shadow-sm text-[11px] leading-snug',
                                'bg-white text-gray-800 border border-gray-200 rounded-tl-none' => $msg->sender_type === 'visitor',
                                'bg-[#1f6fa8] text-white rounded-tr-none' => $msg->sender_type === 'agent',
                                'bg-gray-200 text-gray-600 rounded-tr-none' => $msg->sender_type === 'ai',
                            ])>
                                @if($msg->sender_type !== 'visitor')<span class="block text-[8px] font-semibold uppercase tracking-wide opacity-70 mb-0.5">{{ $msg->sender_name ?: ($msg->sender_type === 'ai' ? 'AI Assistant' : 'Agent') }}</span>@endif<span class="whitespace-pre-wrap">{{ $msg->content }}</span><span class="block text-[8px] opacity-60 mt-0.5">{{ $msg->created_at->format('g:i A') }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="flex-1 flex items-center justify-center text-sm text-gray-400 py-10">
                            No messages yet.
                        </div>
                    @endforelse
                </div>

                @if(! in_array($conversation->status, ['closed', 'returned_to_ai']))
                    <form wire:submit.prevent="sendReply" class="p-4 border-t border-[var(--bf-border)] flex items-center gap-3">
                        <input
                            type="text"
                            wire:model="replyContent"
                            placeholder="Type your reply..."
                            class="flex-1 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#4a9bd1]"
                        />
                        <button
                            type="submit"
                            class="bg-[#1f6fa8] hover:bg-[#1b5a88] text-white text-sm font-semibold rounded-xl px-5 py-2.5 transition-colors"
                        >
                            Send
                        </button>
                    </form>
                    @error('replyContent')
                        <p class="px-4 pb-3 text-xs text-danger-600">{{ $message }}</p>
                    @enderror
                @else
                    <div class="p-4 border-t border-[var(--bf-border)] text-center text-xs text-gray-400">
                        {{ $conversation->status === 'returned_to_ai' ? 'This conversation was handed back to the AI.' : 'This conversation is closed.' }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</x-filament-panels::page>
