<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 p-6">
            {{ $this->form }}
        </div>

        <div class="flex justify-end">
            <x-filament::button type="submit" wire:target="save">
                Save Changes
            </x-filament::button>
        </div>
    </form>

    @if($this->isWidgetReady())
        <div
            x-data="{
                copied: false,
                copy() {
                    navigator.clipboard.writeText($refs.snippet.innerText);
                    this.copied = true;
                    setTimeout(() => (this.copied = false), 2000);
                },
            }"
            class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 overflow-hidden mt-6"
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
