<div class="overflow-x-auto">
    <table class="min-w-full text-sm">
        <thead>
            <tr class="border-b text-left text-gray-500">
                <th class="py-2">Item</th>
                <th class="py-2 text-right">Income</th>
                <th class="py-2 text-right">Expenses</th>
                <th class="py-2 text-right">Profit</th>
            </tr>
        </thead>
        <tbody>
        @forelse($rows as $row)
            <tr class="border-b">
                <td class="py-2">{{ $labelResolver($row) }}</td>
                <td class="py-2 text-right text-emerald-700">KES {{ number_format($row->income, 2) }}</td>
                <td class="py-2 text-right text-rose-700">KES {{ number_format($row->expenses, 2) }}</td>
                <td class="py-2 text-right font-semibold {{ $row->profit >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">KES {{ number_format($row->profit, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="4" class="py-4 text-center text-gray-500">No linked records yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
