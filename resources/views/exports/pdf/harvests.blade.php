{{-- PDF partial: Harvests report --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Harvest Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1  { font-size: 18px; text-align: center; margin-bottom: 4px; }
        h3  { font-size: 14px; color: #555; text-align: center; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #2d6a4f; color: #fff; }
        tr:nth-child(even) { background: #f5fff5; }
        .grade-a { color:#155724; font-weight:bold; }
        .grade-b { color:#004085; }
        .grade-c { color:#856404; }
        .meta { margin-bottom: 12px; }
        .stats { margin-bottom: 16px; }
    </style>
</head>
<body>
    <h1>🌾 SmartShamba — Harvest Report</h1>
    <h3>{{ $farmLabel }}</h3>

    <div class="meta">Generated: {{ $generatedAt }}</div>

    <div class="stats">
        <strong>Summary:</strong>
        Total harvested: {{ number_format($stats['total_qty'], 2) }} kg &nbsp;|&nbsp;
        Total losses: {{ number_format($stats['losses'], 2) }} kg &nbsp;|&nbsp;
        Grade A: {{ $stats['grade_a_count'] }} &nbsp;|&nbsp;
        Grade B: {{ $stats['grade_b_count'] }} &nbsp;|&nbsp;
        Grade C: {{ $stats['grade_c_count'] }}
    </div>

    <table>
        <tr>
            <th>#</th>
            <th>Date</th>
            <th>Batch</th>
            <th>Crop</th>
            <th>Field</th>
            <th>Quantity (kg)</th>
            <th>Grade</th>
            <th>Losses (kg)</th>
            <th>Storage</th>
            <th>Buyer</th>
        </tr>
        @foreach ($harvests as $idx => $h)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $h->harvest_date->format('Y-m-d') }}</td>
            <td>{{ $h->harvest_batch ?? '—' }}</td>
            <td>{{ $h->crop?->name ?? 'N/A' }}</td>
            <td>{{ $h->field?->name ?? 'N/A' }}</td>
            <td>{{ number_format($h->quantity_harvested, 2) }}</td>
            <td class="{{
                match($h->quality_grade) {
                    \App\Models\Harvest::GRADE_A  => 'grade-a',
                    \App\Models\Harvest::GRADE_B  => 'grade-b',
                    \App\Models\Harvest::GRADE_C  => 'grade-c',
                    default => ''
                }
            }}">
                {{ $h->quality_label }}
            </td>
            <td>{{ $h->loss_quantity > 0 ? number_format($h->loss_quantity, 2).' ('.$h->loss_percentage.'%)' : '—' }}</td>
            <td>{{ $h->storage_location_label }}</td>
            <td>{{ $h->buyer_reference ?? '—' }}</td>
        </tr>
        @endforeach
        @if($harvests->isEmpty())
        <tr><td colspan="10" style="text-align:center;color:#888;">No harvest records found.</td></tr>
        @endif
    </table>
</body>
</html>
