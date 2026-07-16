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
</x-filament-panels::page>
