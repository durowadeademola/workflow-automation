<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Unsubscribed</title>
    <style>
        body { font-family: -apple-system, Arial, sans-serif; background: #f3f4f6; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; }
        .card { background: #fff; border-radius: 16px; padding: 40px; max-width: 420px; text-align: center; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h1 { font-size: 20px; color: #111827; margin-bottom: 8px; }
        p { color: #6b7280; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        @if ($found)
            <h1>You've been unsubscribed</h1>
            <p>You won't receive any more marketing messages from this business. This doesn't affect any appointments you've already booked.</p>
        @else
            <h1>Link no longer valid</h1>
            <p>This unsubscribe link has already been used or has expired.</p>
        @endif
    </div>
</body>
</html>
