@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Attachments</h1></div></header>
    @if(session('status'))<p class="module-card">{{ session('status') }}</p>@endif
    @include('documents::partials-filters')
    @include('documents::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>File</th><th>Category</th><th>Source</th><th>Farm</th><th>Uploaded</th><th></th></tr></thead><tbody>@forelse($attachments as $attachment)<tr><td>{{ $attachment->title }}</td><td>{{ $attachment->original_filename }}</td><td>{{ $attachment->category?->name ?? 'Uncategorized' }}</td><td>{{ $attachment->attachable_module ?? 'general' }} / {{ $attachment->attachable_type ?? 'general' }}</td><td>{{ $attachment->farm?->name ?? 'General' }}</td><td>{{ $attachment->created_at->toDateString() }}</td><td><a href="{{ route('documents.attachments.show', $attachment) }}">View</a></td></tr>@empty<tr><td colspan="7">No attachments found.</td></tr>@endforelse</tbody></table></div>
    {{ $attachments->links() }}
@endsection
