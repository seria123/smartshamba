{{-- PDF partial: Livestock report --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Livestock Report</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1  { font-size: 18px; text-align: center; margin-bottom: 4px; }
        h3  { font-size: 14px; color: #555; text-align: center; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; }
        th { background: #2d6a4f; color: #fff; }
        tr:nth-child(even) { background: #f5fff5; }
        .meta { margin-bottom: 12px; }
    </style>
</head>
<body>
    <h1>🌾 SmartShamba — Livestock Report</h1>
    <h3>{{ $farmLabel }}</h3>
    <div class="meta">
        Generated: {{ $generatedAt }} &nbsp;|&nbsp; Total animals: {{ $total }}
        @if($byStatus->isNotEmpty())
            &nbsp;|&nbsp;
            @foreach($byStatus as $status => $count)
                {{ ucfirst($status) }}: {{ $count }}&nbsp;&nbsp;
            @endforeach
        @endif
    </div>

    <table>
        <tr>
            <th>#</th>
            <th>Tag Number</th>
            <th>Tracking ID</th>
            <th>Name</th>
            <th>Type</th>
            <th>Farm</th>
            <th>Status</th>
            <th>Gender</th>
            <th>Weight (kg)</th>
            <th>Date Acquired</th>
            <th>Purchase Price</th>
            <th>Sale Price</th>
        </tr>
        @foreach ($livestock as $idx => $animal)
        <tr>
            <td>{{ $idx + 1 }}</td>
            <td>{{ $animal->tag_number }}</td>
            <td>{{ $animal->tracking_id ?? '—' }}</td>
            <td>{{ $animal->name ?? '—' }}</td>
            <td>{{ $animal->type->name ?? 'N/A' }}</td>
            <td>{{ $animal->farm->name ?? 'N/A' }}</td>
            <td>{{ ucfirst($animal->status) }}</td>
            <td>{{ ucfirst($animal->gender) }}</td>
            <td>{{ $animal->weight ?? '—' }}</td>
            <td>{{ $animal->date_acquired?->format('Y-m-d') ?? '—' }}</td>
            <td>{{ $animal->purchase_price ?? '—' }}</td>
            <td>{{ $animal->sale_price ?? '—' }}</td>
        </tr>
        @endforeach
        @if($livestock->isEmpty())
        <tr><td colspan="12" style="text-align:center;color:#888;">No livestock records found.</td></tr>
        @endif
    </table>
</body>
</html>
