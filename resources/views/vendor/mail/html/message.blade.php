<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{-- A remote asset() URL would point at whatever APP_URL happens to be
     (often a local-only dev domain like blueflow.test) — unreachable from a
     real recipient's mail client, so the logo would just be a broken image
     for anyone outside this machine. Embedded as a real inline attachment
     instead (see AppServiceProvider's MessageSending listener, which
     attaches the logo under this exact Content-ID), so it always renders
     regardless of domain or whether remote images are blocked. --}}
<img src="cid:blueflow-logo@blueflow" class="logo" alt="{{ config('app.name') }}">
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. {{ __('All rights reserved.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
