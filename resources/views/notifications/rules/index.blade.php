@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Notifications / Alerts</p><h1>Notification rules</h1></div><div class="actions"><a class="button" href="{{ route('notifications.rules.create') }}">New rule</a></div></header>
    @if(session('status'))<p class="module-card">{{ session('status') }}</p>@endif
    @include('notifications::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Source</th><th>Signal</th><th>Severity</th><th>Farm</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($rules as $rule)
            <tr><td>{{ $rule->name }}</td><td>{{ $rule->source_module }}</td><td>{{ $rule->signal_type }}</td><td>{{ ucfirst($rule->severity) }}</td><td>{{ $rule->farm?->name ?? 'Organization-wide' }}</td><td>{{ $rule->is_active ? 'Active' : 'Inactive' }}</td><td class="actions"><a href="{{ route('notifications.rules.edit', $rule) }}">Edit</a><form method="POST" action="{{ route('notifications.rules.toggle', $rule) }}">@csrf<button class="button secondary" type="submit">Toggle</button></form></td></tr>
        @empty
            <tr><td colspan="7">No notification rules found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
