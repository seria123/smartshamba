@extends('layouts.app')

@section('content')
    <header class="content-header">
        <div>
            <p class="eyebrow">{{ $label }}</p>
            <h1>{{ $item->name }}</h1>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route($routeBase.'.index') }}">Back</a>
            <a class="button" href="{{ route($routeBase.'.edit', $item) }}">Edit</a>
        </div>
    </header>

    <dl class="detail-list">
        <div><dt>Organization</dt><dd>{{ $item->organization->name }}</dd></div>
        <div><dt>Farm</dt><dd>{{ $item->farm->name }}</dd></div>
        @if ($allowsSite)
            <div><dt>Site</dt><dd>{{ $item->site?->name ?? 'None' }}</dd></div>
        @endif
        <div><dt>Code</dt><dd>{{ $item->code ?? 'Not set' }}</dd></div>
        @if ($allowsArea)
            <div><dt>Area</dt><dd>{{ $item->area ? $item->area.' '.$item->area_unit : 'Not set' }}</dd></div>
        @endif
        @if ($allowsDescription)
            <div><dt>Description</dt><dd>{{ $item->description ?? 'Not set' }}</dd></div>
        @endif
        <div><dt>Status</dt><dd>{{ ucfirst($item->status) }}</dd></div>
    </dl>
@endsection
