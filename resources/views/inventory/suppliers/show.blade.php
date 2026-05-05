@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Supplier</p><h1>{{ $supplier->name }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('inventory.suppliers.index') }}">Back</a><a class="button" href="{{ route('inventory.suppliers.edit',$supplier) }}">Edit</a><form method="POST" action="{{ route('inventory.suppliers.deactivate',$supplier) }}">@csrf<button type="submit">Deactivate</button></form></div></header>
    <dl class="detail-list"><div><dt>Organization</dt><dd>{{ $supplier->organization->name }}</dd></div><div><dt>Code</dt><dd>{{ $supplier->code ?? 'Not set' }}</dd></div><div><dt>Type</dt><dd>{{ $supplier->supplier_type }}</dd></div><div><dt>Status</dt><dd>{{ ucfirst($supplier->status) }}</dd></div></dl>
@endsection
