<div class="space-y-2">
    @forelse($pageResults as $page)
        <div @class([
            'rounded-lg border p-3 flex items-start gap-3',
            'border-emerald-200 dark:border-emerald-900 bg-emerald-50 dark:bg-emerald-950/30' => $page['status'] === 'indexed',
            'border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950/30' => $page['status'] === 'no_content',
            'border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/30' => $page['status'] === 'fetch_failed',
        ])>
            <span @class([
                'text-[10px] font-semibold uppercase tracking-wide rounded-full px-2 py-0.5 shrink-0 mt-0.5',
                'text-emerald-700 bg-emerald-100' => $page['status'] === 'indexed',
                'text-amber-700 bg-amber-100' => $page['status'] === 'no_content',
                'text-red-700 bg-red-100' => $page['status'] === 'fetch_failed',
            ])>
                {{ match($page['status']) {
                    'indexed' => 'OK',
                    'no_content' => 'No content',
                    'fetch_failed' => 'Failed',
                    default => $page['status'],
                } }}
            </span>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 break-all">{{ $page['url'] }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $page['message'] }}</p>
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-500">No pages were crawled.</p>
    @endforelse
</div>
