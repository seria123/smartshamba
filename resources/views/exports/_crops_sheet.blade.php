{{-- _crops_sheet.blade.php --}}
<table>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Variety</th>
        <th>Category</th>
        <th>Field</th>
        <th>Farm</th>
        <th>Status</th>
        <th>Planting Date</th>
        <th>Expected Harvest</th>
        <th>Yield / ha</th>
        <th>Days to Maturity</th>
        <th>Min Temp (°C)</th>
        <th>Max Temp (°C)</th>
        <th>Notes</th>
    </tr>
    @foreach ($crops as $idx => $c)
    <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $c->name }}</td>
        <td>{{ $c->variety ?? 'N/A' }}</td>
        <td>{{ $c->category ?? 'N/A' }}</td>
        <td>{{ $c->field?->name ?? 'N/A' }}</td>
        <td>{{ $c->field?->farm?->name ?? 'N/A' }}</td>
        <td>{{ $c->status ?? 'N/A' }}</td>
        <td>{{ $c->planting_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $c->expected_harvest_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $c->average_yield_per_hectare ?? 'N/A' }}</td>
        <td>{{ $c->days_to_maturity ?? 'N/A' }}</td>
        <td>{{ $c->min_temperature ?? 'N/A' }}</td>
        <td>{{ $c->max_temperature ?? 'N/A' }}</td>
        <td>{{ $c->notes ?? '' }}</td>
    </tr>
    @endforeach
</table>
