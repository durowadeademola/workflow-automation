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

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 p-6 mt-6">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100 mb-1">Two-Factor Authentication</h3>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
            Add an extra layer of security to your account — using an authenticator app like Google Authenticator or Authy, a code emailed to you at login, or both.
        </p>
        {{ $this->twoFactorAuthenticationSchema }}
    </div>
</x-filament-panels::page>
