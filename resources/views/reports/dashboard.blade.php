@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Farm performance dashboard</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <section class="summary-grid">
        @foreach($cards as $card)
            <article class="summary-card">
                <span>{{ $card['label'] }}</span>
                <strong>
                    @if($card['percent'] ?? false)
                        {{ $card['value'] === null ? '—' : number_format($card['value'], 1).'%' }}
                    @elseif($card['money'] ?? false)
                        KES {{ number_format($card['value'], 2) }}
                    @else
                        {{ $card['value'] }}
                    @endif
                </strong>
            </article>
        @endforeach
    </section>
    <section class="grid-two">
        <article>
            <h2>Recent sales</h2>
            <div class="table-wrap"><table><thead><tr><th>Date</th><th>Customer</th><th>Total</th><th>Balance</th></tr></thead><tbody>
                @forelse($recentSales as $sale)
                    <tr><td>{{ $sale->sale_date?->toDateString() }}</td><td>{{ $sale->customer?->name ?? 'Unspecified' }}</td><td>{{ $sale->currency }} {{ number_format($sale->total_amount, 2) }}</td><td>{{ $sale->currency }} {{ number_format($sale->balance_amount, 2) }}</td></tr>
                @empty
                    <tr><td colspan="4">No sales found.</td></tr>
                @endforelse
            </tbody></table></div>
        </article>
        <article>
            <h2>Recent costs</h2>
            <div class="table-wrap"><table><thead><tr><th>Date</th><th>Cost</th><th>Category</th><th>Amount</th></tr></thead><tbody>
                @forelse($recentCosts as $cost)
                    <tr><td>{{ $cost->entry_date?->toDateString() }}</td><td>{{ $cost->title }}</td><td>{{ $cost->category?->name ?? 'Uncategorized' }}</td><td>{{ $cost->currency }} {{ number_format($cost->amount, 2) }}</td></tr>
                @empty
                    <tr><td colspan="4">No costs found.</td></tr>
                @endforelse
            </tbody></table></div>
        </article>
    </section>
@endsection
