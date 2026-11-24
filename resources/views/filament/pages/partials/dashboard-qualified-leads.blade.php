{{-- Qualified-leads gauge, shared by the client and agent dashboards (custom-dashboard.blade.php). --}}
<section class="bf-panel">
    <header class="bf-panel-header">
        <h3 class="bf-panel-title">Qualified leads</h3>
        <p class="bf-panel-desc">Of all customers</p>
    </header>
    @if (($dashboard['totalCustomers'] ?? 0) === 0)
        <p class="bf-panel-empty">No customers yet.</p>
    @else
        @php
            $size = 88; $stroke = 6; $r = ($size - $stroke) / 2; $c = 2 * M_PI * $r;
            $rate = $dashboard['qualifiedRate'];
        @endphp
        <div class="bf-panel-body flex items-center gap-5">
            <div class="relative flex-none" role="img" aria-label="{{ $rate }}% qualified">
                <svg width="{{ $size }}" height="{{ $size }}" viewBox="0 0 {{ $size }} {{ $size }}" aria-hidden="true" class="-rotate-90">
                    <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke="var(--bf-border)" stroke-width="{{ $stroke }}" />
                    <circle cx="{{ $size / 2 }}" cy="{{ $size / 2 }}" r="{{ $r }}" fill="none" stroke="var(--bf-text)" stroke-width="{{ $stroke }}" stroke-linecap="round" stroke-dasharray="{{ $c * $rate / 100 }} {{ $c }}" />
                </svg>
                <span class="bf-gauge-value absolute inset-0 flex items-center justify-center">{{ $rate }}%</span>
            </div>
            <dl class="m-0 grid flex-1 gap-2">
                <div class="flex justify-between gap-3">
                    <dt class="bf-panel-meta">Qualified</dt>
                    <dd class="bf-panel-text m-0 font-semibold tabular-nums">{{ $dashboard['qualifiedLeads'] }}</dd>
                </div>
                <div class="flex justify-between gap-3">
                    <dt class="bf-panel-meta">Total customers</dt>
                    <dd class="bf-panel-text m-0 font-semibold tabular-nums">{{ $dashboard['totalCustomers'] }}</dd>
                </div>
            </dl>
        </div>
    @endif
</section>
