<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Blueflow') }}</title>

        <!-- Applies the saved theme before first paint, so there's no
             flash of the wrong theme. Default is light — dark is opt-in
             only, never OS-preference-based. Shares the same "theme" key
             Filament's /admin and /user panels use, so a toggle on either
             side carries over to the other. -->
        <script>
            (function () {
                try {
                    if (localStorage.getItem('theme') === 'dark') {
                        document.documentElement.classList.add('dark');
                    }
                } catch (e) {}
            })();
        </script>

        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="192x192" href="{{ asset('favicon-192x192.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

        <!-- Fonts (matches the Inter font used in the Filament admin panel) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @viteReactRefresh
        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @inertiaHead
    </head>
    {{-- bg-gray-900 matches Footer's own (always-dark, no light-mode
         variant) background — body's background is otherwise never visible
         during normal use, it only shows through in the gap the bottom
         overscroll bounce reveals below the footer, so this keeps that gap
         blending in instead of showing a mismatched white seam. --}}
    <body class="font-sans antialiased bg-gray-900">
        @inertia

        {{-- Blueflow's own site uses client_id 4 ("Blueflow Automation
             (Internal)") for its own chat widget — generated the exact
             same way any other client's embed snippet is, straight from
             their actual dashboard-configured Widget Settings, so this
             never drifts out of sync with what's actually stored there
             (agent name, quick replies, working hours, anything else). --}}
        {!! \App\Models\Client::find(4)?->getWidgetEmbedSnippet() !!}
    </body>
</html>
