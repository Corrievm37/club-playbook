<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Session</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <div style="max-width: 600px; margin: 0 auto; padding: 16px;">
        <h2 style="margin: 0 0 12px;">New {{ ucfirst($session->type) }} Session</h2>
        <p style="margin: 0 0 8px;">Age Group: <strong>{{ $session->age_group }}</strong></p>
        <p style="margin: 0 0 8px;">When: <strong>{{ $session->scheduled_at->format('Y-m-d H:i') }}</strong></p>
        <p style="margin: 0 0 8px;">Location: <strong>{{ $session->location ?? '—' }}</strong></p>
        @if(!empty($session->title))
            <p style="margin: 0 0 8px;">Title: <strong>{{ $session->title }}</strong></p>
        @endif
        @if(!empty($session->notes))
            <p style="margin: 0 0 8px; white-space: pre-wrap;">Notes: {{ $session->notes }}</p>
        @endif
        <p style="margin-top: 16px;">Please log in to your guardian portal to RSVP.</p>
    </div>
</body>
</html>
