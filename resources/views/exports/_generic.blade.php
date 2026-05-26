{{-- Generic XLSX partial, used by all old-style XLSX exports via GenericExport class --}}
<table>
    @foreach ($rows as $row)
    <tr>
        @foreach ($row as $cell)
        <td>{{ $cell }}</td>
        @endforeach
    </tr>
    @endforeach
</table>
