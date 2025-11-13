<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Team Sheet — {{ $team->name }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Ubuntu, Cantarell, Noto Sans, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji"; margin: 20px; }
        h1 { font-size: 20px; margin: 0 0 10px; }
        .meta { font-size: 12px; color: #444; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #ddd; padding: 6px 8px; font-size: 13px; }
        th { background: #f3f4f6; text-align: left; }
        .print-actions { margin: 10px 0 20px; }
        .print-actions button { display: inline-block; padding: 6px 10px; font-size: 12px; border: 1px solid #111; background: #fff; cursor: pointer; }
        @media print { .print-actions { display: none; } body { margin: 0; } h1 { margin-top: 0; } }
    </style>
</head>
<body>
<div class="print-actions">
    <button onclick="window.print()">Print</button>
</div>
<h1>{{ ucfirst($session->type) }} — {{ $session->age_group }} — {{ $session->scheduled_at->format('Y-m-d H:i') }} — Team: {{ $team->name }}</h1>
<div class="meta">
    <div><strong>Title:</strong> {{ $session->title ?? '—' }}</div>
    <div><strong>Location:</strong> {{ $session->location ?? '—' }}</div>
    <div><strong>Notes:</strong> {{ $session->notes ?? '—' }}</div>
</div>

<table>
    <thead>
    <tr>
        <th style="width: 90px;">Jersey #</th>
        <th>Player</th>
        <th>School</th>
        <th>DOB (Age)</th>
    </tr>
    </thead>
    <tbody>
    @forelse($assignments as $a)
        <tr>
            <td>{{ $a->jersey_number ?? '—' }}</td>
            <td>{{ $a->player->last_name }}, {{ $a->player->first_name }}</td>
            <td>{{ $a->player->school_name ?? '—' }}</td>
            <td>
                @php
                    $dob = $a->player->date_of_birth ? \Carbon\Carbon::parse($a->player->date_of_birth) : null;
                    if (!$dob && !empty($a->player->sa_id_number) && preg_match('/^(\d{2})(\d{2})(\d{2})/', $a->player->sa_id_number, $m)) {
                        $yy = (int)$m[1]; $mm = (int)$m[2]; $dd = (int)$m[3];
                        $century = $yy >= 50 ? 1900 : 2000;
                        try { $dob = \Carbon\Carbon::createFromDate($century + $yy, $mm, $dd); } catch (Exception $e) { $dob = null; }
                    }
                    $age = $dob ? (int) $dob->diffInYears($session->scheduled_at) : null;
                @endphp
                {{ $dob ? $dob->format('d/m/y') : '—' }}{{ $age !== null ? ' ('.$age.')' : '' }}
            </td>
        </tr>
    @empty
        <tr><td colspan="4">No players assigned.</td></tr>
    @endforelse
    </tbody>
</table>
</body>
</html>
