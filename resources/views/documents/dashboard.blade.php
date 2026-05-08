@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Documents dashboard</h1></div></header>
    @if(session('status'))<p class="module-card">{{ session('status') }}</p>@endif
    @include('documents::partials-filters')
    @include('documents::partials-nav')
    <section class="summary-grid"><article class="summary-card"><span>Total active attachments</span><strong>{{ $totalAttachments }}</strong></article><article class="summary-card"><span>Total storage used</span><strong>{{ number_format($totalStorage / 1024, 1) }} KB</strong></article><article class="summary-card"><span>Uploads this month</span><strong>{{ $uploadsThisMonth }}</strong></article></section>
    <h2>Recent uploads</h2>
    <div class="table-wrap"><table><thead><tr><th>Title</th><th>Category</th><th>Farm</th><th>Size</th><th>Uploaded by</th><th></th></tr></thead><tbody>@forelse($recentUploads as $attachment)<tr><td>{{ $attachment->title }}</td><td>{{ $attachment->category?->name ?? 'Uncategorized' }}</td><td>{{ $attachment->farm?->name ?? 'General' }}</td><td>{{ number_format($attachment->file_size_bytes / 1024, 1) }} KB</td><td>{{ $attachment->uploader?->name ?? '—' }}</td><td><a href="{{ route('documents.attachments.show', $attachment) }}">View</a></td></tr>@empty<tr><td colspan="6">No attachments found.</td></tr>@endforelse</tbody></table></div>
@endsection
