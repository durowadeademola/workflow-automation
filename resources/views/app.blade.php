<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Blueflow') }}</title>

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
    <body class="font-sans antialiased">
        @inertia

        <script>
            window.ChatWidgetConfig = {
                clientId:     4,
                apiBase:      '{{ url('/api') }}',
                businessName: 'Blueflow Automation',
                agentName:    'Blueflow Assistant',
                primaryColor: '#2563EB',
                waNumber:     '2347064706193',
                greeting:     '👋 Hello! How can I help you today?',
                systemPrompt: 'We build ai chat widgets, whatsapp automation, payment automation, email automation, custom automation and many more. Be friendly and concise. if the user asks for something not related to the automation, politely let them know you can only assist with automation-related inquiries. If they ask for a human agent, provide the contact information.',
                quickReplies: ['services', 'pricing', 'contact'],
            };
        </script>
            {{-- https://blueflowautomation.com/chat-widget.js --}}
            <script src="{{ asset('chat-widget.js') }}"></script>
    </body>
</html>
