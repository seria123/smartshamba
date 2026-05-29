{{-- PDF partial: Farm summary --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Farm Summary</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; }
        h1  { font-size: 20px; text-align: center; margin-bottom: 4px; }
        h3  { font-size: 14px; color: #555; text-align: center; margin-bottom: 20px; }
        h4  { font-size: 14px; border-bottom: 2px solid #2d6a4f; padding-bottom: 4px; margin-top: 20px; }
        .section { margin-bottom: 18px; }
        .farm-meta { margin-bottom: 16px; }
        .farm-meta span { display: inline-block; margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #999; padding: 6px 8px; text-align: left; font-size: 11px; }
        th { background: #2d6a4f; color: #fff; }
        tr:nth-child(even) { background: #f5fff5; }
        .stat-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 8px; }
        .stat-cell { display: table-cell; width: 20%; background: #eafaf1; border: 1px solid #b7e4c7; padding: 12px; text-align: center; border-radius: 4px; }
        .stat-num { font-size: 22px; font-weight: bold; color: #2d6a4f; }
        .stat-label { font-size: 12px; color: #555; }
    </style>
</head>
<body>
    <h1>🌾 SmartShamba — Farm Summary</h1>
    <h3>{{ $farm?->name ?? 'Farm Report' }}</h3>

    <div class="farm-meta">
        @if($farm?->location)       <span><strong>Location:</strong> {{ $farm->location }}</span> @endif
        @if($farm?->farm_type)       <span><strong>Type:</strong> {{ $farm->farm_type }}</span> @endif
        @if($farm?->size_hectares)   <span><strong>Size:</strong> {{ $farm->size_hectares }} ha</span> @endif
        @if($farm?->main_purpose)    <span><strong>Purpose:</strong> {{ $farm->main_purpose }}</span> @endif
        @if($farm?->ownership_type)  <span><strong>Ownership:</strong> {{ $farm->ownership_type }}</span> @endif
    </div>

    <div class="stat-grid">
        <div class="stat-cell">
            <div class="stat-num">{{ $stats['fields_count'] }}</div>
            <div class="stat-label">Fields</div>
        </div>
        <div class="stat-cell">
            <div class="stat-num">{{ $stats['livestock_count'] }}</div>
            <div class="stat-label">Livestock</div>
        </div>
        <div class="stat-cell">
            <div class="stat-num">{{ $stats['crops_count'] }}</div>
            <div class="stat-label">Crop Cycles</div>
        </div>
        <div class="stat-cell">
            <div class="stat-num">{{ $stats['equipment_count'] }}</div>
            <div class="stat-label">Equipment</div>
        </div>
        <div class="stat-cell">
            <div class="stat-num">{{ $stats['harvest_records'] }}</div>
            <div class="stat-label">Harvest Records</div>
        </div>
    </div>

    {{-- Livestock --}}
    <div class="section">
        <h4>🐄 Livestock ({{ $farm?->livestock()->count() ?? 0 }})</h4>
        <table>
            <tr>
                <th>#</th><th>Tag Number</th><th>Name</th><th>Type</th>
                <th>Status</th><th>Gender</th><th>Weight (kg)</th><th>Date Acquired</th>
            </tr>
            @foreach ($farm?->livestock ?? [] as $idx => $animal)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $animal->tag_number }}</td>
                <td>{{ $animal->name ?? '—' }}</td>
                <td>{{ $animal->type->name ?? 'N/A' }}</td>
                <td>{{ ucfirst($animal->status) }}</td>
                <td>{{ ucfirst($animal->gender) }}</td>
                <td>{{ $animal->weight ?? '—' }}</td>
                <td>{{ $animal->date_acquired?->format('Y-m-d') ?? '—' }}</td>
            </tr>
            @endforeach
            @if(($farm?->livestock ?? collect())->isEmpty())
            <tr><td colspan="8" style="text-align:center;color:#888;">No livestock records.</td></tr>
            @endif
        </table>
    </div>

    {{-- Equipment --}}
    <div class="section">
        <h4>🔧 Equipment ({{ $farm?->equipment()->count() ?? 0 }})</h4>
        <table>
            <tr>
                <th>#</th><th>Name</th><th>Type</th><th>Model</th><th>Serial</th>
                <th>Status</th><th>Condition</th><th>Purchase Date</th>
            </tr>
            @foreach ($farm?->equipment ?? [] as $idx => $eq)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $eq->name }}</td>
                <td>{{ $eq->type }}</td>
                <td>{{ $eq->model_number ?? '—' }}</td>
                <td>{{ $eq->serial_number ?? '—' }}</td>
                <td>{{ ucfirst($eq->status) }}</td>
                <td>{{ ucfirst($eq->condition) }}</td>
                <td>{{ $eq->purchase_date?->format('Y-m-d') ?? '—' }}</td>
            </tr>
            @endforeach
            @if(($farm?->equipment ?? collect())->isEmpty())
            <tr><td colspan="8" style="text-align:center;color:#888;">No equipment records.</td></tr>
            @endif
        </table>
    </div>

    {{-- Crop Cycles --}}
    <div class="section">
        <h4>🌱 Crop Cycles ({{ $farm?->cropCycles()->count() ?? 0 }})</h4>
        <table>
            <tr>
                <th>#</th><th>Crop</th><th>Field</th><th>Status</th>
                <th>Start Date</th><th>End Date</th><th>Seed Variety</th>
            </tr>
            @foreach ($farm?->cropCycles ?? [] as $idx => $cycle)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $cycle->crop?->name ?? 'N/A' }}</td>
                <td>{{ $cycle->field?->name ?? 'N/A' }}</td>
                <td>{{ $cycle->status }}</td>
                <td>{{ $cycle->start_date?->format('Y-m-d') ?? '—' }}</td>
                <td>{{ $cycle->expected_harvest_date?->format('Y-m-d') ?? '—' }}</td>
                <td>{{ $cycle->variety ?? '—' }}</td>
            </tr>
            @endforeach
            @if(($farm?->cropCycles ?? collect())->isEmpty())
            <tr><td colspan="7" style="text-align:center;color:#888;">No crop cycle records.</td></tr>
            @endif
        </table>
    </div>

    <div style="margin-top:24px;text-align:center;color:#aaa;font-size:10px;">
        Generated by SmartShamba on {{ $generatedAt }}
    </div>
</body>
</html>
