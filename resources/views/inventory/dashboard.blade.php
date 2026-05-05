@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Inventory / Inputs</p>
            <h1>Inventory dashboard</h1>
            <p class="lede">Foundation records for products, suppliers, stock lots, and inventory movements.</p>
        </div>
    </header>

    <section class="summary-grid" aria-label="Inventory summary">
        @foreach ($counts as $label => $count)
            <article class="summary-card"><strong>{{ $count }}</strong><span>{{ $label }}</span></article>
        @endforeach
    </section>

    <section class="actions">
        <a class="button" href="{{ route('inventory.products.index') }}">Products</a>
        <a class="button" href="{{ route('inventory.suppliers.index') }}">Suppliers</a>
        <a class="button" href="{{ route('inventory.stock.balances') }}">Stock</a>
        <a class="button secondary" href="{{ route('inventory.stock.receive.form') }}">Receive Stock</a>
    </section>
@endsection
