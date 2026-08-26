<x-filament-panels::page>
    {{-- recrawl() dispatches this once it's found issues rather than
         calling mountAction() directly — see the comment in
         WidgetSettings::recrawl() for why nesting a mountAction() call
         inside the recrawl action's own request handling doesn't work. --}}
    <div x-data x-on:recrawl-finished.window="$wire.mountAction('recrawlResults')"></div>

    @if($this->isWidgetReady())
        <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6 flex items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Widget Status</h3>
                <p class="text-xs text-gray-400 mt-0.5">
                    {{ $this->isWidgetEnabled()
                        ? 'Your assistant is live and answering visitors right now.'
                        : "Your assistant is turned off — visitors won't get a response until you turn it back on." }}
                </p>
            </div>
            <button
                type="button"
                wire:click="toggleWidgetEnabled"
                wire:loading.attr="disabled"
                @class([
                    'shrink-0 relative inline-flex h-7 w-12 items-center rounded-full transition-colors',
                    'bg-emerald-500' => $this->isWidgetEnabled(),
                    'bg-gray-300 dark:bg-gray-700' => ! $this->isWidgetEnabled(),
                ])
                aria-label="Toggle widget on or off"
            >
                <span @class([
                    'inline-block h-5 w-5 transform rounded-full bg-white transition-transform',
                    'translate-x-6' => $this->isWidgetEnabled(),
                    'translate-x-1' => ! $this->isWidgetEnabled(),
                ])></span>
            </button>
        </div>
    @endif

    <form wire:submit="save" class="space-y-6">
        <div class="bg-white dark:bg-[#111827] rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            {{ $this->form }}
        </div>

        <div class="flex justify-end gap-3">
            @if($this->canRecrawl())
                <x-filament::button
                    type="button"
                    color="gray"
                    wire:click="mountAction('recrawl')"
                >
                    Recrawl my site
                </x-filament::button>
            @endif
            <x-filament::button type="submit" wire:target="save">
                Save Changes
            </x-filament::button>
        </div>
    </form>

    @if($this->isWidgetReady())
        <div
            x-data="{
                copied: false,
                async copy() {
                    const text = $refs.snippet.innerText;

                    // navigator.clipboard only exists in a secure context
                    // (HTTPS, or localhost) — plain HTTP, and any denied
                    // clipboard permission even on HTTPS, both leave it
                    // undefined/rejecting. Fall back to the old
                    // select-and-execCommand approach, which works
                    // regardless, rather than silently doing nothing.
                    try {
                        if (! navigator.clipboard) {
                            throw new Error('Clipboard API unavailable');
                        }
                        await navigator.clipboard.writeText(text);
                    } catch (e) {
                        const range = document.createRange();
                        range.selectNodeContents($refs.snippet);
                        const selection = window.getSelection();
                        selection.removeAllRanges();
                        selection.addRange(range);
                        document.execCommand('copy');
                        selection.removeAllRanges();
                    }

                    this.copied = true;
                    setTimeout(() => (this.copied = false), 2000);
                },
            }"
            class="bg-white dark:bg-[#111827] rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden mt-6"
        >
            <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center justify-between gap-4">
                <div>
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Your Embed Code</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Paste this just before the closing <code>&lt;/body&gt;</code> tag on your website. Save your changes above first.</p>
                </div>
                <button
                    type="button"
                    @click="copy()"
                    class="shrink-0 text-xs font-semibold px-3 py-1.5 rounded-lg bg-primary-600 hover:bg-primary-700 text-white transition-colors"
                >
                    <span x-show="!copied">Copy code</span>
                    <span x-show="copied" x-cloak>Copied!</span>
                </button>
            </div>
            <pre x-ref="snippet" class="p-4 text-xs leading-relaxed text-gray-700 dark:text-gray-300 overflow-x-auto whitespace-pre">{{ $this->getEmbedSnippet() }}</pre>
        </div>
    @else
        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-900 rounded-xl p-6 mt-6 flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-900 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-300">Your embed code isn't ready yet</h3>
                <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">
                    Save your customization above, and our team will finish setting things up on our end. We'll notify you the
                    moment it's ready to embed and go live — no action needed from you in the meantime.
                </p>
            </div>
        </div>
    @endif
</x-filament-panels::page>
