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
</x-filament-panels::page>
