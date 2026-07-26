<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Workflow Studio</title>
    <!-- Matches the Inter font used in the Filament admin panel and the main site -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #0f172a;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        }
        .card {
            width: 100%;
            max-width: 360px;
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 12px;
            padding: 32px;
        }
        h1 {
            color: #f1f5f9;
            font-size: 1.25rem;
            margin: 0 0 4px;
        }
        p.sub {
            color: #94a3b8;
            font-size: 0.875rem;
            margin: 0 0 24px;
        }
        label {
            display: block;
            color: #cbd5e1;
            font-size: 0.8rem;
            margin-bottom: 6px;
        }
        input {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 16px;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 8px;
            color: #f1f5f9;
            font-size: 0.9rem;
        }
        input:focus {
            outline: none;
            border-color: #6366f1;
        }
        button {
            width: 100%;
            padding: 10px 12px;
            background: #4f46e5;
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
        }
        button:hover { background: #4338ca; }
        .error {
            background: #450a0a;
            border: 1px solid #7f1d1d;
            color: #fecaca;
            font-size: 0.8rem;
            padding: 10px 12px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Workflow Studio</h1>
        <p class="sub">Native Workflow Engine</p>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('workflow-studio.login.attempt') }}">
            @csrf
            <label for="username">Username</label>
            <input type="text" id="username" name="username" autocomplete="username" required autofocus>

            <label for="password">Password</label>
            <input type="password" id="password" name="password" autocomplete="current-password" required>

            <button type="submit">Sign in</button>
        </form>
    </div>
</body>
</html>
