{{-- Rendered at CONTENT_START by both panel providers. The panels run without Filament's topbar,
     so this is the notifications bell's only home: pinned to the top-right of the content. --}}
@if (filament()->auth()->check() && filament()->hasDatabaseNotifications())
    <div class="bf-content-topbar">
        @livewire(Filament\Livewire\DatabaseNotifications::class, [
            'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
        ])
    </div>
@endif
