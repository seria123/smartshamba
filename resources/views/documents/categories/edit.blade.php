@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Edit category</h1></div></header>
    <form method="POST" action="{{ route('documents.categories.update', $category) }}">@method('PUT')@include('documents::categories.form')</form>
@endsection
