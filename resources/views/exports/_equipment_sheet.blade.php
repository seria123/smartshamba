{{-- _equipment_sheet.blade.php --}}
<table>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Type</th>
        <th>Model Number</th>
        <th>Serial Number</th>
        <th>Farm</th>
        <th>Status</th>
        <th>Condition</th>
        <th>Purchase Date</th>
        <th>Purchase Cost</th>
        <th>Assigned To</th>
        <th>Last Serviced</th>
        <th>Next Service</th>
        <th>Description</th>
    </tr>
    @foreach ($equipment as $idx => $eq)
    <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $eq->name }}</td>
        <td>{{ $eq->type ?? 'N/A' }}</td>
        <td>{{ $eq->model_number ?? 'N/A' }}</td>
        <td>{{ $eq->serial_number ?? 'N/A' }}</td>
        <td>{{ $eq->farm?->name ?? 'N/A' }}</td>
        <td>{{ $eq->status ?? 'N/A' }}</td>
        <td>{{ $eq->condition ?? 'N/A' }}</td>
        <td>{{ $eq->purchase_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $eq->purchase_cost ?? 'N/A' }}</td>
        <td>{{ $eq->assignedStaff?->name ?? 'Unassigned' }}</td>
        <td>{{ $eq->last_service_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $eq->next_service_date?->format('Y-m-d') ?? 'N/A' }}</td>
        <td>{{ $eq->description ?? '' }}</td>
    </tr>
    @endforeach
</table>
