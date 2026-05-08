@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Notifications / Alerts</p><h1>{{ $notification->title }}</h1></div><div class="actions"><a class="button secondary" href="{{ route('notifications.index') }}">Back</a></div></header>
    @if(session('status'))<p class="module-card">{{ session('status') }}</p>@endif
    <dl class="detail-list">
        <div><dt>Status</dt><dd>{{ ucfirst($notification->status) }}</dd></div>
        <div><dt>Severity</dt><dd>{{ ucfirst($notification->severity) }}</dd></div>
        <div><dt>Message</dt><dd>{{ $notification->message }}</dd></div>
        <div><dt>Source</dt><dd>{{ $notification->source_module }} / {{ $notification->source_label ?? $notification->source_type }}</dd></div>
        <div><dt>Due</dt><dd>{{ $notification->due_at?->toDateString() ?? '—' }}</dd></div>
        <div><dt>Generated</dt><dd>{{ $notification->generated_at?->toDayDateTimeString() ?? '—' }}</dd></div>
        @if($notification->action_url)<div><dt>Source link</dt><dd><a href="{{ $notification->action_url }}">Open source record</a></dd></div>@endif
    </dl>
    @if(auth()->user()->canAccessAdmin('notifications.manage'))
        <div class="actions" style="margin-top: 20px;">
            <form method="POST" action="{{ route('notifications.mark-read', $notification) }}">@csrf<button class="button secondary" type="submit">Mark read</button></form>
            <form method="POST" action="{{ route('notifications.mark-unread', $notification) }}">@csrf<button class="button secondary" type="submit">Mark unread</button></form>
            <form method="POST" action="{{ route('notifications.dismiss', $notification) }}">@csrf<button class="button secondary" type="submit">Dismiss</button></form>
            <form method="POST" action="{{ route('notifications.resolve', $notification) }}">@csrf<button class="button" type="submit">Resolve</button></form>
        </div>
    @endif
@endsection
