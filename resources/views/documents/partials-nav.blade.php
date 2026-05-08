<div class="actions" style="margin-bottom: 24px;">
    <a class="button secondary" href="{{ route('documents.dashboard') }}">Dashboard</a>
    <a class="button secondary" href="{{ route('documents.attachments.index') }}">Attachments</a>
    @if(auth()->user()->canAccessAdmin('documents.manage'))
        <a class="button" href="{{ route('documents.attachments.create') }}">Upload</a>
        <a class="button secondary" href="{{ route('documents.categories.index') }}">Categories</a>
    @endif
    <a class="button secondary" href="{{ route('documents.reports.summary') }}">Summary</a>
    <a class="button secondary" href="{{ route('documents.reports.by-category') }}">By category</a>
    <a class="button secondary" href="{{ route('documents.reports.by-source') }}">By source</a>
</div>
