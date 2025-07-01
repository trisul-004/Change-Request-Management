<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Activity Timeline PDF</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        .header { margin-bottom: 20px; }
        .logo-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 10px; }
        .logo { height: 48px; }
        .meta { font-size: 11px; color: #555; text-align: right; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 5px; }
        .subtitle { font-size: 14px; color: #555; margin-bottom: 10px; }
        .timeline { border-left: 3px solid #3490dc; padding-left: 15px; margin-top: 20px; }
        .note { margin-bottom: 18px; }
        .note-type { font-size: 11px; font-weight: bold; color: #3490dc; margin-right: 8px; }
        .note-date { font-size: 11px; color: #888; margin-left: 8px; }
        .note-content { margin-top: 2px; margin-bottom: 2px; }
        .note-author { font-size: 11px; color: #555; }
        .info-table { width: 100%; margin-top: 10px; margin-bottom: 10px; border-collapse: collapse; }
        .info-table td { padding: 4px 8px; font-size: 12px; }
        .info-label { color: #555; font-weight: bold; width: 120px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-row">
            <img src="{{ public_path('ongc-logo.png') }}" class="logo" alt="ONGC Logo">
            <div class="meta">
                Downloaded: {{ $downloadedAt->format('M d, Y H:i') }}
            </div>
        </div>
        <div class="title">Activity Timeline</div>
        <div class="subtitle">Task: {{ $changeRequest->title }}</div>
        <div>Description: {{ $changeRequest->description }}</div>
        <table class="info-table">
            <tr>
                <td class="info-label">Supervisor:</td>
                <td>{{ $supervisor ? $supervisor->name : 'N/A' }}</td>
                <td class="info-label">Developer:</td>
                <td>{{ $developer ? $developer->name : 'N/A' }}</td>
            </tr>
        </table>
    </div>
    <div class="timeline">
        @forelse($developerNotes as $note)
            <div class="note">
                <span class="note-type">
                    {{ ucfirst(str_replace('_', ' ', $note->action_type)) }}
                </span>
                <span class="note-date">
                    {{ $note->created_at->format('M d, Y H:i') }}
                </span>
                <div class="note-content">{{ $note->notes }}</div>
                <div class="note-author">By: {{ $note->developer->name }}</div>
                @if($note->action_type === 'status_change')
                    <div class="note-status">Status: {{ ucfirst($note->status_before) }} → {{ ucfirst($note->status_after) }}</div>
                @endif
            </div>
        @empty
            <div>No activity yet.</div>
        @endforelse
    </div>
</body>
</html> 