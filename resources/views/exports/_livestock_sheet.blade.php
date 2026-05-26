{{-- _livestock_sheet.blade.php — single sheet for Maatwebsite Excel --}}
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
        <th>Birth Date</th>
        <th>Purchase Price</th>
        <th>Sale Price</th>
        <th>Notes</th>
    </tr>
    @foreach ($livestock as $idx => $animal)
    <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $animal->tag_number }}</td>
        <td>{{ $animal->tracking_id ?? 'N/A' }}</td>
        <td>{{ $animal->name ?? 'N/A' }}</td>
        <td>{{ $animal->type->name ?? 'N/A' }}</td>
        <td>{{ $animal->farm->name ?? 'N/A' }}</td>
        <td>{{ ucfirst($animal->status) }}</td>
        <td>{{ ucfirst($animal->gender) }}</td>
        <td>{{ $animal->weight ?? 'N/A' }}</td>
        <td>{{ $animal->date_acquired?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $animal->birth_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $animal->purchase_price ?? 'N/A' }}</td>
        <td>{{ $animal->sale_price ?? 'N/A' }}</td>
        <td>{{ $animal->notes ?? '' }}</td>
    </tr>
    @endforeach
</table>
