@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">{{ $product->code }}</p><h1>{{ $product->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('inventory.products.index') }}">Back</a><a class="button" href="{{ route('inventory.products.edit',$product) }}">Edit</a><form method="POST" action="{{ route('inventory.products.deactivate',$product) }}">@csrf<button type="submit">Deactivate</button></form></div></header>
    <dl class="detail-list"><div><dt>Category</dt><dd>{{ $product->category->name }}</dd></div><div><dt>Type</dt><dd>{{ $product->product_type }}</dd></div><div><dt>Unit</dt><dd>{{ $product->unit_of_measure }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($product->status) }}</dd></div><div><dt>Default cost</dt><dd>{{ $product->default_unit_cost }}</dd></div></dl>
@endsection
