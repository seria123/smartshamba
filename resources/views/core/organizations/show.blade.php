@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">Organization</p>
            <h1>{{ $organization->name }}</h1>
        </div>
        <a class="button secondary" href="{{ route('core.organizations.index') }}">Back</a>
    </header>

    <dl class="detail-list">
        <div><dt>Slug</dt><dd>{{ $organization->slug }}</dd></div>
        <div><dt>Status</dt><dd>{{ ucfirst($organization->status) }}</dd></div>
        <div><dt>Farms</dt><dd>{{ $organization->farms->count() }}</dd></div>
        <div><dt>Sites</dt><dd>{{ $organization->sites->count() }}</dd></div>
        <div><dt>Fields</dt><dd>{{ $organization->fields->count() }}</dd></div>
        <div><dt>Paddocks</dt><dd>{{ $organization->paddocks->count() }}</dd></div>
        <div><dt>Warehouses</dt><dd>{{ $organization->warehouses->count() }}</dd></div>
    </dl>
@endsection
