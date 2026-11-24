{{-- Right half of the split login/auth screens, rendered at SIMPLE_LAYOUT_END by both panel providers
     and styled by the .bf-auth-* rules in theme.css (hidden below the lg breakpoint, where only the
     form shows). Drop a screenshot of the app at public/images/login-side.{jpg,png,webp} and it
     shows framed, peeking in from the right; until then the tagline stands in. --}}
@php
    $image = collect(['jpg', 'jpeg', 'png', 'webp'])
        ->map(fn (string $extension): string => "images/login-side.{$extension}")
        ->first(fn (string $path): bool => file_exists(public_path($path)));
@endphp

<aside class="bf-auth-aside" aria-hidden="true">
    @if ($image)
        <img src="{{ asset($image) }}" alt="" class="bf-auth-aside-shot">
    @else
        <div class="bf-auth-aside-copy">
            <p class="bf-auth-aside-title">Automate everything. Grow faster.</p>
            <p class="bf-auth-aside-text">
                Agentic AI automation for your business — save time, reduce costs, and scale effortlessly.
            </p>
        </div>
    @endif
</aside>
