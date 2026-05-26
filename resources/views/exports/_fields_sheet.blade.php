{{-- _fields_sheet.blade.php --}}
<table>
    <tr>
        <th>#</th>
        <th>Name</th>
        <th>Farm</th>
        <th>Size (ha)</th>
        <th>Location</th>
        <th>Soil Type</th>
        <th>Water Source</th>
        <th>GPS Latitude</th>
        <th>GPS Longitude</th>
        <th>Topography</th>
        <th>Rainfall Zone</th>
        <th>Description</th>
    </tr>
    @foreach ($fields as $idx => $f)
    <tr>
        <td>{{ $idx + 1 }}</td>
        <td>{{ $f->name }}</td>
        <td>{{ $f->farm?->name ?? 'N/A' }}</td>
        <td>{{ $f->size_hectares ?? 'N/A' }}</td>
        <td>{{ $f->location ?? 'N/A' }}</td>
        <td>{{ $f->soil_type ?? 'N/A' }}</td>
        <td>{{ $f->water_source ?? 'N/A' }}</td>
        <td>{{ $f->gps_latitude ?? 'N/A' }}</td>
        <td>{{ $f->gps_longitude ?? 'N/A' }}</td>
        <td>{{ $f->topography ?? 'N/A' }}</td>
        <td>{{ $f->rainfall_zone ?? 'N/A' }}</td>
        <td>{{ $f->description ?? '' }}</td>
    </tr>
    @endforeach
</table>
