<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Blueflow') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @viteReactRefresh
        @vite(['resources/js/app.jsx', "resources/js/Pages/{$page['component']}.jsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia

        <script>
            window.ChatWidgetConfig = {
                webhookUrl:   'https://unrepugnant-silas-anecdotically.ngrok-free.dev/webhook/chat-widget',
                clientId:     'medicare-abuja',
                businessName: 'MediCare Abuja',
                agentName:    'MediCare Assistant',
                primaryColor: '#2563EB',
                waNumber:     '2347064706193',
                greeting:     '👋 Hello! How can I help you today?',
                //systemPrompt: 'You are a helpful AI assistant for MediCare Abuja. Help with appointment bookings, clinic hours, and services. Be friendly and concise. if the user asks for something not related to the clinic, politely let them know you can only assist with clinic-related inquiries. If they ask for a human agent, provide the contact information.',
                quickReplies: ['Book appointment', 'Clinic hours', 'Our services', 'Talk to a human'],
            };
            </script>
            {{-- https://blueflowautomation.com/chat-widget.js --}}
            <script src="{{ asset('chat-widget.js') }}"></script>
    </body>
</html>
