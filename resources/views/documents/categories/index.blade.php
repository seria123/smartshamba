@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Attachment categories</h1></div><div class="actions"><a class="button" href="{{ route('documents.categories.create') }}">New category</a></div></header>
    @if(session('status'))<p class="module-card">{{ session('status') }}</p>@endif
    @include('documents::partials-nav')
    <div class="table-wrap"><table><thead><tr><th>Name</th><th>Scope</th><th>Status</th><th>System</th><th></th></tr></thead><tbody>@forelse($categories as $category)<tr><td>{{ $category->name }}</td><td>{{ $category->organization_id ? 'Organization' : 'All organizations' }}</td><td>{{ $category->is_active ? 'Active' : 'Inactive' }}</td><td>{{ $category->is_system ? 'Yes' : 'No' }}</td><td class="actions"><a href="{{ route('documents.categories.edit', $category) }}">Edit</a>@if(! $category->is_system)<form method="POST" action="{{ route('documents.categories.destroy', $category) }}">@csrf @method('DELETE')<button class="button secondary" type="submit">Delete</button></form>@endif</td></tr>@empty<tr><td colspan="5">No categories found.</td></tr>@endforelse</tbody></table></div>
@endsection
