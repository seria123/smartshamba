@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Transfer stock</h1></div><a class="button secondary" href="{{ route('inventory.stock.balances') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ route('inventory.stock.transfer') }}">@csrf @include('inventory::partials.stock-scope')
        <input type="hidden" name="movement_type" value="transfer">
        @include('inventory::partials.lot-quantity-reason')
        <div class="field-group"><label for="to_warehouse_id">Destination warehouse</label><select id="to_warehouse_id" name="to_warehouse_id" required><option value="">Select warehouse</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }} - {{ $warehouse->farm->name }}</option>@endforeach</select></div>
        <button type="submit">Transfer stock</button>
    </form>
@endsection
