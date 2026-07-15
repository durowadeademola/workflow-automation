<x-filament-panels::page>
    @php
        $latest = $this->getLatestSubmission();
    @endphp

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 p-6 mb-6">
        @if(! $latest)
            <div class="text-center py-4">
                <p class="text-sm text-gray-500">
                    You haven't submitted identity verification yet. It's optional, but approved businesses may get access to extra features in the future.
                </p>
            </div>
        @elseif($latest->status === 'pending')
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600">Under Review</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Submitted {{ $latest->submitted_at?->diffForHumans() }}</h3>
                    <p class="text-sm text-gray-500 mt-1">We'll notify you once it's been reviewed.</p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wide text-amber-700 bg-amber-100 rounded-full px-3 py-1">
                    Pending
                </span>
            </div>
        @elseif($latest->status === 'approved')
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-emerald-600">Verified</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Your identity has been verified</h3>
                    <p class="text-sm text-gray-500 mt-1">Reviewed {{ $latest->reviewed_at?->format('M j, Y') }}.</p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wide text-emerald-700 bg-emerald-100 rounded-full px-3 py-1">
                    Approved
                </span>
            </div>
        @else
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-red-600">Not Approved</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Your submission was rejected</h3>
                    <p class="text-sm text-gray-500 mt-1">{{ $latest->rejection_reason ?? 'No reason was given.' }}</p>
                </div>
                <span class="text-xs font-semibold uppercase tracking-wide text-red-700 bg-red-100 rounded-full px-3 py-1">
                    Rejected
                </span>
            </div>
        @endif
    </div>

    @if($this->canSubmit())
        <form wire:submit="submit" class="space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 p-6">
                {{ $this->form }}
            </div>

            <div class="flex justify-end">
                <x-filament::button type="submit">
                    {{ $latest ? 'Resubmit' : 'Submit for Review' }}
                </x-filament::button>
            </div>
        </form>
    @endif
</x-filament-panels::page>
