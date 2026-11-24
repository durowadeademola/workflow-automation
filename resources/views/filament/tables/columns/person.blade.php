{{-- Cell markup for App\Filament\Tables\Columns\PersonColumn; styled by .bf-person in theme.css. --}}
<div class="bf-person">
    <span class="bf-person-avatar" aria-hidden="true">{{ $initials }}</span>
    <span class="bf-person-text">
        <span class="bf-person-name">{{ $name }}</span>
        @if (filled($subline))
            <span class="bf-person-subline">{{ $subline }}</span>
        @endif
    </span>
</div>
