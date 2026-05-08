@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Reports / Analytics</p><h1>Operational activity</h1></div></header>
    @include('reports::partials.filters')
    @include('reports::partials.nav')
    <section class="summary-grid">
        @foreach($sections as $label => $value)
            <article class="summary-card"><span>{{ $label }}</span><strong>{{ $value }}</strong></article>
        @endforeach
    </section>
    <h2>Open tasks</h2>
    <div class="table-wrap"><table><thead><tr><th>Task</th><th>Farm</th><th>Status</th><th>Due Date</th></tr></thead><tbody>
        @forelse($openTasks as $task)
            <tr><td>{{ $task->title }}</td><td>{{ $task->farm?->name ?? '—' }}</td><td>{{ $task->status }}</td><td>{{ $task->due_date?->toDateString() }}</td></tr>
        @empty
            <tr><td colspan="4">No open tasks found.</td></tr>
        @endforelse
    </tbody></table></div>
@endsection
