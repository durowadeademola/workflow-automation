<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="config('app.url')">
{{-- Plain text rather than a logo image: a remote asset() URL would be
     broken for real recipients until the production domain is actually
     live, and embedding it as an inline attachment (tried previously) made
     Gmail's mobile app show a paperclip/attachment icon on every email even
     though there's nothing to actually download. --}}
{{ config('app.name') }}
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
<br>
<a href="{{ route('privacy-policy') }}">Privacy Policy</a> &middot; <a href="{{ route('terms-of-service') }}">Terms of Service</a>
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
