@extends('layouts.app')
@section('content')
    <header class="content-header"><div><p class="eyebrow">Documents / Attachments</p><h1>Edit attachment</h1></div></header>
    <form method="POST" action="{{ route('documents.attachments.update', $attachment) }}">@method('PUT')@include('documents::attachments.form')</form>
@endsection
