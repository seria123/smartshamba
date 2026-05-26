{{-- _harvests_sheet.blade.php --}}
<table>
    <tr>
        <th>#</th>
        <th>Date</th>
        <th>Batch</th>
        <th>Crop</th>
        <th>Field</th>
        <th>Farm</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Grade</th>
        <th>Loss Quantity</th>
        <th>Loss Reason</th>
        <th>Moisture %</th>
        <th>Storage</th>
        <th>Destination</th>
        <th>Buyer Reference</th>
        <th>Notes</th>
    </tr>
    @foreach ($harvests as $idx => $h)
    <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $h->harvest_date->format('Y-m-d') }}</td>
        <td>{{ $h->harvest_batch ?? 'N/A' }}</td>
        <td>{{ $h->crop?->name ?? 'N/A' }}</td>
        <td>{{ $h->field?->name ?? 'N/A' }}</td>
        <td>{{ $h->farm?->name ?? 'N/A' }}</td>
        <td>{{ $h->quantity_harvested }}</td>
        <td>{{ $h->unit ?? 'N/A' }}</td>
        <td>{{ $h->quality_label }}</td>
        <td>{{ $h->loss_quantity ?? 0 }}</td>
        <td>{{ $h->loss_reason ?? 'N/A' }}</td>
        <td>{{ $h->moisture_content ?? 'N/A' }}</td>
        <td>{{ $h->storage_location_label }}</td>
        <td>{{ $h->destination_label ?? 'N/A' }}</td>
        <td>{{ $h->buyer_reference ?? 'N/A' }}</td>
        <td>{{ $h->notes ?? '' }}</td>
    </tr>
    @endforeach
</table>
