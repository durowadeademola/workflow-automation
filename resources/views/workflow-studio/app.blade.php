<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="logout-url" content="{{ route('workflow-studio.logout') }}">
    <title>Workflow Studio</title>
    <!-- Matches the Inter font used in the Filament admin panel and the main site -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @viteReactRefresh
    @vite(['resources/js/workflow-studio/main.jsx'])
</head>
<body>
    <div id="workflow-studio-root"></div>
</body>
</html>
