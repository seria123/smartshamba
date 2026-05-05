@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Inventory</p><h1>Adjust stock</h1></div><a class="button secondary" href="{{ route('inventory.stock.balances') }}">Back</a></header>
    <form class="form-panel" method="POST" action="{{ route('inventory.stock.adjust') }}">@csrf @include('inventory::partials.stock-scope')
        <div class="field-group"><label for="movement_type">Adjustment type</label><select id="movement_type" name="movement_type" required>@foreach(['adjustment_in','adjustment_out','damage','loss','expiry_disposal','correction'] as $type)<option value="{{ $type }}">{{ str_replace('_',' ',ucfirst($type)) }}</option>@endforeach</select></div>
        @include('inventory::partials.lot-quantity-reason')
        <button type="submit">Adjust stock</button>
    </form>
@endsection
