@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Maintenance record</p><h1>{{ $record->record_number }}</h1></div><a class="button secondary" href="{{ route('assets.maintenance-records.index') }}">Back</a></header>
    <section class="detail-grid"><article><strong>Asset</strong><p>{{ $record->asset?->name }}</p></article><article><strong>Date</strong><p>{{ $record->maintenance_date?->toDateString() }}</p></article><article><strong>Type</strong><p>{{ str_replace('_',' ',$record->maintenance_type) }}</p></article><article><strong>Status</strong><p>{{ $record->status }}</p></article><article><strong>Product</strong><p>{{ $record->product_name_snapshot ?? $record->product?->name ?? 'None' }}</p></article><article><strong>External cost</strong><p>{{ $record->external_cost ?? 'None' }}</p></article></section><h2>Work done</h2><p>{{ $record->work_done }}</p><p>{{ $record->notes }}</p>
@endsection
