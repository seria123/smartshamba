@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Farm</p>
            <h1>{{ $farm->name }}</h1>
        </div>
        <a class="button secondary" href="{{ route('core.farms.index') }}">Back</a>
    </header>

    <dl class="detail-list">
        <div><dt>Organization</dt><dd>{{ $farm->organization->name }}</dd></div>
        <div><dt>Code</dt><dd>{{ $farm->code ?? 'Not set' }}</dd></div>
        <div><dt>Status</dt><dd>{{ ucfirst($farm->status) }}</dd></div>
        <div><dt>Sites</dt><dd>{{ $farm->sites->count() }}</dd></div>
        <div><dt>Fields</dt><dd>{{ $farm->fields->count() }}</dd></div>
        <div><dt>Paddocks</dt><dd>{{ $farm->paddocks->count() }}</dd></div>
        <div><dt>Warehouses</dt><dd>{{ $farm->warehouses->count() }}</dd></div>
    </dl>
@endsection
