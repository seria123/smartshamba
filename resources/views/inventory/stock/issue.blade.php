@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Issue stock</h1></div><a class="button secondary" href="{{ route('inventory.stock.balances') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ route('inventory.stock.issue') }}">@csrf @include('inventory::partials.stock-scope')
        <input type="hidden" name="movement_type" value="issue">
        @include('inventory::partials.lot-quantity-reason')
        <button type="submit">Issue stock</button>
    </form>
@endsection
