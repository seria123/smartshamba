@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Receive stock</h1></div><a class="button secondary" href="{{ route('inventory.stock.balances') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ route('inventory.stock.receive') }}">@csrf
        @include('inventory::partials.stock-scope')
        <div class="field-group"><label for="warehouse_id">Warehouse</label><select id="warehouse_id" name="warehouse_id" required><option value="">Select warehouse</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }} - {{ $warehouse->farm->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="product_id">Product</label><select id="product_id" name="product_id" required><option value="">Select product</option>@foreach($products as $product)<option value="{{ $product->id }}">{{ $product->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="supplier_id">Supplier</label><select id="supplier_id" name="supplier_id"><option value="">No supplier</option>@foreach($suppliers as $supplier)<option value="{{ $supplier->id }}">{{ $supplier->name }}</option>@endforeach</select></div>
        <div class="field-group"><label for="movement_type">Movement type</label><select id="movement_type" name="movement_type" required>@foreach(['purchase_receipt','opening_balance','adjustment_in'] as $type)<option value="{{ $type }}">{{ str_replace('_',' ',ucfirst($type)) }}</option>@endforeach</select></div>
        @foreach(['lot_number'=>'Lot number','batch_number'=>'Batch number','unit_of_measure'=>'Unit','reason'=>'Reason'] as $field=>$label)<div class="field-group"><label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" @if($field==='lot_number') required @endif></div>@endforeach
        <div class="field-group"><label for="expiry_date">Expiry date</label><input id="expiry_date" name="expiry_date" type="date"></div>
        <div class="field-group"><label for="quantity">Quantity</label><input id="quantity" name="quantity" type="number" min="0.01" step="0.01" required></div>
        <div class="field-group"><label for="unit_cost">Unit cost</label><input id="unit_cost" name="unit_cost" type="number" min="0" step="0.01" required></div>
        <div class="field-group"><label for="currency">Currency</label><input id="currency" name="currency" maxlength="3" value="KES"></div>
        <button type="submit">Receive stock</button>
    </form>
@endsection
