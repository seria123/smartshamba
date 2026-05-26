{{-- _cropcycles_sheet.blade.php --}}
<table>
    <tr>
        <th>#</th>
        <th>Crop</th>
        <th>Field</th>
        <th>Farm</th>
        <th>Status</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Seed Variety</th>
        <th>Seed Source</th>
        <th>Fertilizer Used</th>
        <th>Pesticides</th>
        <th>Notes</th>
    </tr>
    @foreach ($cropCycles as $idx => $cycle)
    <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $cycle->crop?->name ?? 'N/A' }}</td>
        <td>{{ $cycle->field?->name ?? 'N/A' }}</td>
        <td>{{ $cycle->field?->farm?->name ?? 'N/A' }}</td>
        <td>{{ $cycle->status ?? 'N/A' }}</td>
        <td>{{ $cycle->start_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $cycle->end_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $cycle->seed_variety ?? 'N/A' }}</td>
        <td>{{ $cycle->seed_source ?? 'N/A' }}</td>
        <td>{{ $cycle->fertilizer_used ?? 'N/A' }}</td>
        <td>{{ $cycle->pesticides_used ?? 'N/A' }}</td>
        <td>{{ $cycle->notes ?? '' }}</td>
    </tr>
    @endforeach
</table>
