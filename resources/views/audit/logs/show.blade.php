@extends('layouts.app')
@php
    $jsonBlock = function ($value) {
        return $value ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : 'None';
    };
@endphp
@section('content')
    <header class="content-header"><div><p class="eyebrow">Audit Trail</p><h1>Activity detail</h1></div><a class="button secondary" href="{{ route('audit.logs.index') }}">Back to logs</a></header>
    <section class="module-card">
        <dl class="detail-list">
            <dt>Timestamp</dt><dd>{{ $log->occurred_at?->format('Y-m-d H:i:s') }}</dd>
            <dt>Actor</dt><dd>{{ $log->actor_display_name }} @if($log->actor_email)<small>{{ $log->actor_email }}</small>@endif</dd>
            <dt>Organization / farm</dt><dd>{{ $log->organization?->name ?? 'System' }} / {{ $log->farm?->name ?? 'All farms' }}</dd>
            <dt>Module</dt><dd>{{ $log->module_label }}</dd>
            <dt>Event / action</dt><dd>{{ $log->event_label }} <small>{{ $log->event }}</small></dd>
            <dt>Subject</dt><dd>{{ $subjectLabel }}</dd>
            <dt>Description</dt><dd>{{ $log->description ?? 'None' }}</dd>
            <dt>Request</dt><dd>{{ $log->request_method ?? 'N/A' }} {{ $log->request_url ?? '' }}</dd>
            <dt>IP address</dt><dd>{{ $log->ip_address ?? 'N/A' }}</dd>
            <dt>User agent</dt><dd>{{ $log->user_agent ?? 'N/A' }}</dd>
        </dl>
    </section>
    <section class="summary-grid">
        <article class="module-card"><h2>Before values</h2><pre>{{ $jsonBlock($log->before_values) }}</pre></article>
        <article class="module-card"><h2>After values</h2><pre>{{ $jsonBlock($log->after_values) }}</pre></article>
        <article class="module-card"><h2>Changed values</h2><pre>{{ $jsonBlock($log->changed_values) }}</pre></article>
        <article class="module-card"><h2>Metadata</h2><pre>{{ $jsonBlock($log->metadata) }}</pre></article>
    </section>
@endsection
