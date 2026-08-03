<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family: -apple-system, Arial, sans-serif;">
    <div style="max-width:600px; margin:0 auto; padding:24px;">
        <div style="margin:0 0 20px;">
            <span style="color:#111827; font-weight:bold; font-size:16px; font-family: -apple-system, Arial, sans-serif;">{{ $businessName }}</span>
        </div>

        <div style="background:#ffffff; border-radius:12px; padding:32px; color:#1f2937;">
            {!! $bodyHtml !!}
        </div>

        <p style="text-align:center; color:#9ca3af; font-size:12px; margin-top:20px;">
            You're receiving this newsletter from {{ $businessName }}.
            <a href="{{ $unsubscribeUrl }}" style="color:#6b7280;">Unsubscribe</a>
        </p>
    </div>
</body>
</html>
